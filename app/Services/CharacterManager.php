<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Facades\Settings;
use App\Models\Character\BreedingPermission;
use App\Models\Character\BreedingPermissionLog;
use App\Models\Character\Character;
use App\Models\Character\CharacterBookmark;
use App\Models\Character\CharacterBreedingLog;
use App\Models\Character\CharacterBreedingLogRelation;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Character\CharacterFeature;
use App\Models\Character\CharacterGeneration;
use App\Models\Character\CharacterGenome;
use App\Models\Character\CharacterGenomeGene;
use App\Models\Character\CharacterGenomeGradient;
use App\Models\Character\CharacterGenomeNumeric;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterImageSubtype;
use App\Models\Character\CharacterLineage;
use App\Models\Character\CharacterLog;
use App\Models\Character\CharacterPedigree;
use App\Models\Character\CharacterProfileCustomValue;
use App\Models\Character\CharacterTransfer;
use App\Models\Genetics\Loci;
use App\Models\Rarity;
use App\Models\Sales\SalesCharacter;
use App\Models\Species\Subtype;
use App\Models\User\User;
use App\Models\User\UserCharacterLog;
use App\Models\User\UserPet;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class CharacterManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Character Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of character data.
    |
    */

    /**
     * Retrieves the next number to be used for a character's masterlist code.
     *
     * @param int $categoryId
     *
     * @return string
     */
    public function pullNumber($categoryId) {
        $digits = config('lorekeeper.settings.character_number_digits');
        $result = str_pad('', $digits, '0'); // A default value, in case
        $number = 0;

        // First check if the number needs to be the overall next
        // or next in category, and retrieve the highest number
        if (config('lorekeeper.settings.character_pull_number') == 'all') {
            $character = Character::myo(0)->orderBy('number', 'DESC')->first();
            if ($character) {
                $number = ltrim($character->number, 0);
            }
            if (!strlen($number)) {
                $number = '0';
            }
        } elseif (config('lorekeeper.settings.character_pull_number') == 'category' && $categoryId) {
            $character = Character::myo(0)->where('character_category_id', $categoryId)->orderBy('number', 'DESC')->first();
            if ($character) {
                $number = ltrim($character->number, 0);
            }
            if (!strlen($number)) {
                $number = '0';
            }
        }

        $result = format_masterlist_number($number + 1, $digits);

        return $result;
    }

    /**
     * Creates a new litter of MYOs.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool|Character
     */
    public function createMyoLitter($data, $user) {
        DB::beginTransaction();
        try {
            // Validate the parents.
            if (!(isset($data['parents'])) || count($data['parents']) < 2) {
                throw new \Exception('There needs to be 2 parents selected.');
            }
            if (count($data['parents']) > 2) {
                throw new \Exception('Breedings of 3 or more parents are not supported.');
            }

            $parents = [];
            foreach ($data['parents'] as $index => $id) {
                $parents[$index] = Character::where('id', $id)->first();
                if (!$parents[$index]) {
                    throw new \Exception("Couldn't find parent #".$index.'.');
                }
                if (!$parents[$index]->genomes) {
                    throw new \Exception('Parent #'.$index." doesn't have a genome.");
                }
            }

            // Get roller settings.
            $settings = [
                'min_offspring'  => isset($data['min_offspring']) ? max(0, $data['min_offspring']) : 0,
                'max_offspring'  => isset($data['max_offspring']) ? max(1, $data['max_offspring']) : 1,
                'twin_chance'    => isset($data['twin_chance']) ? max(0, min(100, $data['twin_chance'])) : 0,
                'twin_depth'     => isset($data['twin_depth']) ? max(0, $data['twin_depth']) : 1,
                'chimera_chance' => isset($data['chimera_chance']) ? max(0, min(100, $data['chimera_chance'])) : 0,
                'max_genomes'    => isset($data['chimera_depth']) ? max(1, $data['chimera_depth']) : 1,
                'litter_limit'   => isset($data['litter_limit']) ? max(1, $data['litter_limit']) : 1,
            ];

            // Create the Breeding Log.
            $litterLog = CharacterBreedingLog::create([
                'name'            => $data['name'] ?? null,
                'roller_settings' => $settings,
                'rolled_at'       => Carbon::now(),
                'user_id'         => $user->id,
            ]);

            // Log the parents.
            foreach ($parents as $parent) {
                $log = CharacterBreedingLogRelation::create([
                    'log_id'       => $litterLog->id,
                    'character_id' => $parent->id,
                    'is_parent'    => true,
                ]);
                if (!$log) {
                    throw new \Exception("Couldn't generate parent log.");
                }
            }

            // Generate the children...
            // *******************************************************

            $bool = isset($data['default_image']);
            $data += ['default_image' => true, 'feature_id' => [], 'feature_data' => []];
            if (isset($data['generate_lineage']) && $data['generate_lineage'] && method_exists($this, 'handleCharacterLineage')) {
                $data += [
                    // Character Lineages
                    'generate_ancestors' => true,
                    'sire_id'            => $parents[1]->id,
                    'dam_id'             => $parents[0]->id,

                    // WB Lineages
                    'parent_type' => ['Character', 'Character'],
                    'parent_data' => [$parents[0]->id, $parents[1]->id],
                ];
            }
            $litter = [];
            $genomes = [];
            for ($i = 0; $i < mt_rand($settings['min_offspring'], $settings['max_offspring']); $i++) {
                // a function inside CharacterGenome that will cross mother's genes with father's.
                // called from the mother's genome to ensure the matrilineal genes go first.
                // random() allows for children of chimera to inherit from different genomes.
                // the method returns the format of genome data used by handleCharacterGenome().
                $genomes = [$parents[0]->genomes->random()->breedWith($parents[1]->genomes->random())];
                $child = $this->createCharacter(
                    ['name' => $data['name'].' #'.(count($litter) + 1)] + $data + $genomes[0],
                    $user,
                    true,
                );
                if (!$child) {
                    throw new \Exception('Failed to generate child!');
                }
                while (mt_rand(1, 100) <= $settings['chimera_chance'] && count($genomes) < $settings['max_genomes']) {
                    $genome = $parents[0]->genomes->random()->breedWith($parents[1]->genomes->random());
                    $geno = $this->handleCharacterGenome($genome, $child);
                    if (!$geno) {
                        throw new \Exception('Chimerism roll failed to create genome.');
                    }
                    array_push($genomes, $genome);
                }

                // Creation finished, add them to the breeding log.
                $log = CharacterBreedingLogRelation::create([
                    'log_id'       => $litterLog->id,
                    'character_id' => $child->id,
                    'is_parent'    => false,
                    'twin_id'      => null,
                    'chimerism'    => count($genomes) > 1,
                ]);
                if (!$log) {
                    throw new \Exception('Failed to generate child log.');
                }

                // The litter size is increasing.
                array_push($litter, $child);
                if (count($litter) >= $settings['litter_limit']) {
                    break;
                }

                // *******************************************************

                $d = 0; // Current twin depth is zero, as we do not have any twins.
                $source = $child->id; // the character id of the current twin's source.
                while (mt_rand(1, 100) <= $settings['twin_chance'] && $d < $settings['twin_depth'] && count($litter) < $settings['litter_limit']) {
                    // Grab this twin's genome from the genomes pool, then reset the pool.
                    $genomes = [$genomes[mt_rand(0, count($genomes) - 1)]];
                    $child = $this->createCharacter(
                        ['name' => $data['name'].' #'.(count($litter) + 1)] + $data + $genomes[0],
                        $user,
                        true,
                    );
                    if (!$child) {
                        throw new \Exception('Failed to generate twin!');
                    }
                    while (mt_rand(1, 100) <= $settings['chimera_chance'] && count($genomes) < $settings['max_genomes']) {
                        $genome = $parents[0]->genomes->random()->breedWith($parents[1]->genomes->random());
                        $geno = $this->handleCharacterGenome($genome, $child);
                        if (!$geno) {
                            throw new \Exception('Twin chimerism roll failed to create genome.');
                        }
                        array_push($genomes, $genome);
                    }

                    // Creation finished, add them to the breeding log.
                    $log = CharacterBreedingLogRelation::create([
                        'log_id'       => $litterLog->id,
                        'character_id' => $child->id,
                        'is_parent'    => false,
                        'twin_id'      => $source,
                        'chimerism'    => count($genomes) > 1,
                    ]);
                    if (!$log) {
                        throw new \Exception('Failed to generate child log.');
                    }

                    // The litter size is increasing.
                    array_push($litter, $child);
                    if (count($litter) >= $settings['litter_limit']) {
                        break 2;
                    }

                    // This child becomes the new source, and the twin depth has increased.
                    $source = $child->id;
                    $d++;
                }
            }

            // *******************************************************
            // The children have generated...

            // Clean up the images we told the character manager not to delete.
            if (!$bool) {
                $this->deleteImage($litter[0]->image->imageDirectory, $litter[0]->image->imageFileName);
                $this->deleteImage($litter[0]->image->imageDirectory, $litter[0]->image->thumbnailFileName);
            }

            return $this->commitReturn($litterLog);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a new character or MYO slot.
     *
     * @param array $data
     * @param User  $user
     * @param bool  $isMyo
     *
     * @return bool|Character
     */
    public function createCharacter($data, $user, $isMyo = false) {
        DB::beginTransaction();

        try {
            if (!$isMyo && Character::where('slug', $data['slug'])->exists()) {
                throw new \Exception('Please enter a unique character code.');
            }

            if (!(isset($data['user_id']) && $data['user_id']) && !(isset($data['owner_url']) && $data['owner_url'])) {
                throw new \Exception('Please select an owner.');
            }
            if (!$isMyo) {
                if (!(isset($data['species_id']) && $data['species_id'])) {
                    throw new \Exception('Characters require a species.');
                }
                if (!(isset($data['rarity_id']) && $data['rarity_id'])) {
                    throw new \Exception('Characters require a rarity.');
                }
            }
            if (isset($data['subtype_ids']) && $data['subtype_ids']) {
                if (count($data['subtype_ids']) > config('lorekeeper.extensions.multiple_subtype_limit')) {
                    throw new \Exception('Too many subtypes selected.');
                }

                if (!(isset($data['species_id']) && $data['species_id'])) {
                    throw new \Exception('Species must be selected to select a subtype.');
                }

                foreach ($data['subtype_ids'] as $subtypeId) {
                    $subtype = Subtype::find($subtypeId);
                    if (!$subtype || $subtype->species_id != $data['species_id']) {
                        throw new \Exception('Selected subtype invalid or does not match species.');
                    }
                }
            } else {
                $data['subtype_ids'] = null;
            }

            if (isset($data['generation_id']) && $data['generation_id']) {
                $generation = CharacterGeneration::find($data['generation_id']);
                if (!$generation) {
                    throw new \Exception('Selected generation is invalid.');
                }
            } else {
                $data['generation_id'] = null;
            }
            if ((isset($data['pedigree_id']) && $data['pedigree_id']) || (isset($data['pedigree_descriptor']) && $data['pedigree_descriptor'])) {
                $pedigree = CharacterPedigree::find($data['pedigree_id']);
                if ((!isset($data['pedigree_descriptor']) && $data['pedigree_id']) || (!isset($data['pedigree_id']) && $data['pedigree_descriptor'])) {
                    throw new \Exception('If you are assigning this character a pedigree name, then both pedigree tag and pedigree descriptor must be set.');
                }
                if (!$pedigree) {
                    throw new \Exception('Selected pedigree tag is invalid.');
                }
            } else {
                $data['pedigree_id'] = null;
                $data['pedigree_descriptor'] = null;
            }

            // Get owner info
            $url = null;
            $recipientId = null;
            if (isset($data['user_id']) && $data['user_id']) {
                $recipient = User::find($data['user_id']);
            } elseif (isset($data['owner_url']) && $data['owner_url']) {
                $recipient = checkAlias($data['owner_url'], false);
            }

            if (is_object($recipient)) {
                $recipientId = $recipient->id;
                $data['user_id'] = $recipient->id;
            } else {
                $url = $recipient;
            }

            // Create character
            $character = $this->handleCharacter($data, $isMyo);
            if (!$character) {
                throw new \Exception('Error happened while trying to create character.');
            }

            // Create character lineage
            if (isset($data['parent_1_id']) || isset($data['parent_1_name']) || isset($data['parent_2_id']) || isset($data['parent_2_name'])) {
                $lineage = $this->handleCharacterLineage($data, $character);
                if (!$lineage) {
                    throw new \Exception('Error happened while trying to create lineage.');
                }
            }

            // Create character image
            $data['is_valid'] = true; // New image of new characters are always valid
            $image = $this->handleCharacterImage($data, $character, $isMyo);
            if (!$image) {
                throw new \Exception('Error happened while trying to create image.');
            }

            // Update the character's image ID
            $character->character_image_id = $image->id;
            $character->save();

            // Can't and shouldn't always create a character with a genome.
            // Try create character genome if there's data for it.
            if (isset($data['gene_id'])) {
                $genome = $this->handleCharacterGenome($data, $character);
                if (!$genome) {
                    throw new \Exception('Error happened while trying to create genome.');
                }
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, $recipientId, $url, $character->id, $isMyo ? 'MYO Slot Created' : 'Character Created', 'Initial upload', 'character');

            // Add a log for the user
            // This logs ownership of the character
            $this->createLog($user->id, null, $recipientId, $url, $character->id, $isMyo ? 'MYO Slot Created' : 'Character Created', 'Initial upload', 'user');

            // Update the user's FTO status and character count
            if (is_object($recipient)) {
                if (!$isMyo) {
                    $recipient->settings->is_fto = 0; // MYO slots don't affect the FTO status - YMMV
                }
                $recipient->settings->save();
            }

            // Grant breeding permission currency to the character if relevant
            if (Settings::get('breeding_permission_autogrant')) {
                if (!(new CurrencyManager)->creditCurrency($user, $character, 'Automatic Breeding Permission Grant', 'Character Created', Settings::get('breeding_permission_currency'), Settings::get('breeding_permission_autogrant'))) {
                    throw new \Exception('An error occurred while granting breeding permissions.');
                }
            }

            // If the recipient has an account, send them a notification
            if (is_object($recipient) && $user->id != $recipient->id) {
                Notifications::create($isMyo ? 'MYO_GRANT' : 'CHARACTER_UPLOAD', $recipient, [
                    'character_url' => $character->url,
                ] + (
                    $isMyo ?
                    ['name' => $character->name] :
                    ['character_slug' => $character->slug]
                ));
            }

            if (!$this->logAdminAction($user, 'Created Character', 'Created '.$character->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($character);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Trims and optionally resizes and watermarks an image.
     *
     * @param CharacterImage $characterImage
     */
    public function processImage($characterImage) {
        $imageProperties = getimagesize($characterImage->imagePath.'/'.$characterImage->imageFileName);
        if ($imageProperties[0] > 2000 || $imageProperties[1] > 2000) {
            // For large images (in terms of dimensions),
            // use imagick instead, as it's better at handling them
            Config::set('image.driver', 'imagick');
        }

        // Trim transparent parts of image.
        $image = Image::make($characterImage->imagePath.'/'.$characterImage->imageFileName)->trim('transparent');

        if (config('lorekeeper.settings.masterlist_image_automation') == 1) {
            // Make the image be square
            $imageWidth = $image->width();
            $imageHeight = $image->height();

            if ($imageWidth > $imageHeight) {
                // Landscape
                $canvas = Image::canvas($image->width(), $image->width());
                $image = $canvas->insert($image, 'center');
            } else {
                // Portrait
                $canvas = Image::canvas($image->height(), $image->height());
                $image = $canvas->insert($image, 'center');
            }
        }

        // Add background fill if destination format is not transparent
        if (!in_array(config('lorekeeper.settings.masterlist_image_format'), ['png', 'webp']) && config('lorekeeper.settings.masterlist_image_format') != null && config('lorekeeper.settings.masterlist_image_background') != null) {
            $canvas = Image::canvas($image->width(), $image->height(), config('lorekeeper.settings.masterlist_image_background'));
            $image = $canvas->insert($image, 'center');
        }

        if (config('lorekeeper.settings.store_masterlist_fullsizes') == 1) {
            // Generate fullsize hash if not already generated,
            // then save the full-sized image
            if (!$characterImage->fullsize_hash) {
                $characterImage->fullsize_hash = randomString(15);
                $characterImage->save();
            }

            if (config('lorekeeper.settings.masterlist_fullsizes_cap') != 0) {
                if ($image->width() > $image->height()) {
                    // Landscape
                    $image->resize(config('lorekeeper.settings.masterlist_fullsizes_cap'), null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    // Portrait
                    $image->resize(null, config('lorekeeper.settings.masterlist_fullsizes_cap'), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
            }

            // Save the processed image
            $image->save($characterImage->imagePath.'/'.$characterImage->fullsizeFileName, 100, config('lorekeeper.settings.masterlist_fullsizes_format') != null ? config('lorekeeper.settings.masterlist_fullsizes_format') : $characterImage->fullsize_extension);
        } else {
            // Delete fullsize if it was previously created.
            if (isset($characterImage->fullsize_hash) ? file_exists(public_path($characterImage->imageDirectory.'/'.$characterImage->fullsizeFileName)) : false) {
                unlink($characterImage->imagePath.'/'.$characterImage->fullsizeFileName);
            }
        }

        // Resize image if desired
        if (config('lorekeeper.settings.masterlist_image_dimension') != 0) {
            if ($image->width() > $image->height()) {
                // Landscape
                if (config('lorekeeper.settings.masterlist_image_dimension_target') == 'shorter') {
                    $image->resize(null, config('lorekeeper.settings.masterlist_image_dimension'), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image->resize(config('lorekeeper.settings.masterlist_image_dimension'), null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
            } else {
                // Portrait
                if (config('lorekeeper.settings.masterlist_image_dimension_target') == 'shorter') {
                    $image->resize(config('lorekeeper.settings.masterlist_image_dimension'), null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                } else {
                    $image->resize(null, config('lorekeeper.settings.masterlist_image_dimension'), function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
            }
        }
        // Watermark the image if desired
        if (config('lorekeeper.settings.watermark_masterlist_images') == 1) {
            $watermark = Image::make('images/watermark.png');

            if (config('lorekeeper.settings.watermark_resizing') == 1) {
                $imageWidth = $image->width();
                $imageHeight = $image->height();

                $wmWidth = $watermark->width();
                $wmHeight = $watermark->height();

                $wmScale = config('lorekeeper.settings.watermark_percent');

                // Assume Landscape by Default
                $maxSize = $imageWidth * $wmScale;

                if ($imageWidth > $imageHeight) {
                    // Landscape
                    $maxSize = $imageWidth * $wmScale;
                } else {
                    // Portrait
                    $maxSize = $imageHeight * $wmScale;
                }

                if ($wmWidth > $wmHeight) {
                    // Landscape
                    $watermark->resize($maxSize, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    // Portrait
                    $watermark->resize(null, $maxSize, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }
            $image->insert($watermark, 'center');
        }

        // Save the processed image
        $image->save($characterImage->imagePath.'/'.$characterImage->imageFileName, 100, config('lorekeeper.settings.masterlist_image_format'));
    }

    /**
     * Crops a thumbnail for the given image.
     *
     * @param array          $points
     * @param CharacterImage $characterImage
     * @param mixed          $isMyo
     */
    public function cropThumbnail($points, $characterImage, $isMyo = false) {
        $imageProperties = getimagesize($characterImage->imagePath.'/'.$characterImage->imageFileName);
        if ($imageProperties[0] > 2000 || $imageProperties[1] > 2000) {
            // For large images (in terms of dimensions),
            // use imagick instead, as it's better at handling them
            Config::set('image.driver', 'imagick');
        }

        $image = Image::make($characterImage->imagePath.'/'.$characterImage->imageFileName);

        if (!in_array(config('lorekeeper.settings.masterlist_image_format'), ['png', 'webp']) && config('lorekeeper.settings.masterlist_image_format') != null && config('lorekeeper.settings.masterlist_image_background') != null) {
            $canvas = Image::canvas($image->width(), $image->height(), config('lorekeeper.settings.masterlist_image_background'));
            $image = $canvas->insert($image, 'center');
            $trimColor = true;
        }

        if (config('lorekeeper.settings.watermark_masterlist_thumbnails') == 1 && !$isMyo) {
            // Trim transparent parts of image.
            $image->trim(isset($trimColor) && $trimColor ? 'top-left' : 'transparent');

            if (config('lorekeeper.settings.masterlist_image_automation') == 1) {
                // Make the image be square
                if ($image->width() > $image->height()) {
                    // Landscape
                    $canvas = Image::canvas($image->width(), $image->width());
                    $image = $canvas->insert($image, 'center');
                } else {
                    // Portrait
                    $canvas = Image::canvas($image->height(), $image->height());
                    $image = $canvas->insert($image, 'center');
                }
            }

            $cropWidth = config('lorekeeper.settings.masterlist_thumbnails.width');
            $cropHeight = config('lorekeeper.settings.masterlist_thumbnails.height');

            $imageWidthOld = $image->width();
            $imageHeightOld = $image->height();

            $trimOffsetX = $imageWidthOld - $image->width();
            $trimOffsetY = $imageHeightOld - $image->height();

            if (config('lorekeeper.settings.watermark_masterlist_images') == 1) {
                // Resize image if desired, so that the watermark is applied to the correct size of image
                if (config('lorekeeper.settings.masterlist_image_dimension') != 0) {
                    if ($image->width() > $image->height()) {
                        // Landscape
                        $image->resize(null, config('lorekeeper.settings.masterlist_image_dimension'), function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    } else {
                        // Portrait
                        $image->resize(config('lorekeeper.settings.masterlist_image_dimension'), null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                }
                // Watermark the image
                $watermark = Image::make('images/watermark.png');

                if (config('lorekeeper.settings.watermark_resizing_thumb') == 1) {
                    $imageWidth = $image->width();
                    $imageHeight = $image->height();

                    $wmWidth = $watermark->width();
                    $wmHeight = $watermark->height();

                    $wmScale = config('lorekeeper.settings.watermark_percent');

                    // Assume Landscape by Default
                    $maxSize = $imageWidth * $wmScale;

                    if ($imageWidth > $imageHeight) {
                        // Landscape
                        $maxSize = $imageWidth * $wmScale;
                    } else {
                        // Portrait
                        $maxSize = $imageHeight * $wmScale;
                    }

                    if ($wmWidth > $wmHeight) {
                        // Landscape
                        $watermark->resize($maxSize, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } else {
                        // Portrait
                        $watermark->resize(null, $maxSize, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                }
                $image->insert($watermark, 'center');
            }
            // Now shrink the image

            $imageWidth = $image->width();
            $imageHeight = $image->height();

            if ($imageWidth > $imageHeight) {
                // Landscape
                $image->resize(null, $cropWidth, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                // Portrait
                $image->resize($cropHeight, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            if (config('lorekeeper.settings.masterlist_image_automation') == 0) {
                $xOffset = 0 + (($points['x0'] - $trimOffsetX) > 0 ? ($points['x0'] - $trimOffsetX) : 0);
                if (($xOffset + $cropWidth) > $image->width()) {
                    $xOffsetNew = $cropWidth - ($image->width() - $xOffset);
                }
                if (isset($xOffsetNew)) {
                    if (($xOffsetNew + $cropWidth) > $image->width()) {
                        $xOffsetNew = $image->width() - $cropWidth;
                    }
                }
                $yOffset = 0 + (($points['y0'] - $trimOffsetY) > 0 ? ($points['y0'] - $trimOffsetY) : 0);
                if (($yOffset + $cropHeight) > $image->height()) {
                    $yOffsetNew = $cropHeight - ($image->height() - $yOffset);
                }
                if (isset($yOffsetNew)) {
                    if (($yOffsetNew + $cropHeight) > $image->height()) {
                        $yOffsetNew = $image->height() - $cropHeight;
                    }
                }

                // Crop according to the selected area
                $image->crop($cropWidth, $cropHeight, $xOffsetNew ?? $xOffset, $yOffsetNew ?? $yOffset);
            }
        } else {
            $cropWidth = $points['x1'] - $points['x0'];
            $cropHeight = $points['y1'] - $points['y0'];

            if (config('lorekeeper.settings.masterlist_image_automation') == 0) {
                // Crop according to the selected area
                $image->crop($cropWidth, $cropHeight, $points['x0'], $points['y0']);
            }

            // Resize to fit the thumbnail size
            $image->resize(config('lorekeeper.settings.masterlist_thumbnails.width'), config('lorekeeper.settings.masterlist_thumbnails.height'));
        }

        // Save the thumbnail
        $image->save($characterImage->thumbnailPath.'/'.$characterImage->thumbnailFileName, 100, config('lorekeeper.settings.masterlist_image_format'));
    }

    /**
     * Creates a character log.
     *
     * @param int    $senderId
     * @param string $senderUrl
     * @param int    $recipientId
     * @param string $recipientUrl
     * @param int    $characterId
     * @param string $type
     * @param string $data
     * @param string $logType
     * @param bool   $isUpdate
     * @param string $oldData
     * @param string $newData
     *
     * @return bool
     */
    public function createLog($senderId, $senderUrl, $recipientId, $recipientUrl, $characterId, $type, $data, $logType, $isUpdate = false, $oldData = null, $newData = null) {
        $log = null;

        $shared = [
            'sender_id'     => $senderId,
            'sender_url'    => $senderUrl,
            'recipient_id'  => $recipientId,
            'recipient_url' => $recipientUrl,
            'character_id'  => $characterId,
            'log'           => $type.($data ? ' ('.$data.')' : ''),
            'log_type'      => $type,
            'data'          => $data,
        ];

        if ($logType == 'character') {
            $log = CharacterLog::create(
                $shared + [
                    'change_log' => $isUpdate ? [
                        'old' => $oldData,
                        'new' => $newData,
                    ] : null,
                ]
            );
        } else {
            $log = UserCharacterLog::create($shared);
        }

        return $log;
    }

    /**
     * Creates a character image.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     *
     * @return bool|Character
     */
    public function createImage($data, $character, $user) {
        DB::beginTransaction();

        try {
            if (!$character->is_myo_slot) {
                if (!(isset($data['species_id']) && $data['species_id'])) {
                    throw new \Exception('Characters require a species.');
                }
                if (!(isset($data['rarity_id']) && $data['rarity_id'])) {
                    throw new \Exception('Characters require a rarity.');
                }
            }
            if (isset($data['subtype_ids']) && $data['subtype_ids']) {
                if (count($data['subtype_ids']) > config('lorekeeper.extensions.multiple_subtype_limit')) {
                    throw new \Exception('Too many subtypes selected.');
                }
                if (!(isset($data['species_id']) && $data['species_id'])) {
                    throw new \Exception('Species must be selected to select a subtype.');
                }
                foreach ($data['subtype_ids'] as $subtypeId) {
                    $subtype = Subtype::find($subtypeId);
                    if (!$subtype || $subtype->species_id != $data['species_id']) {
                        throw new \Exception('Selected subtype invalid or does not match species.');
                    }
                }
            } else {
                $data['subtype_ids'] = null;
            }

            $data['is_visible'] = 1;

            // Create character image
            $image = $this->handleCharacterImage($data, $character);
            if (!$image) {
                throw new \Exception('Error happened while trying to create image.');
            }

            // Update the character's image ID
            $character->character_image_id = $image->id;
            $character->save();

            if (!$this->logAdminAction($user, 'Created Image', 'Created character image <a href="'.$character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, $character->user_id, ($character->user_id ? null : $character->owner_url), $character->id, 'Character Image Uploaded', '[#'.$image->id.']', 'character');

            // If the recipient has an account, send them a notification
            if ($character->user && $user->id != $character->user_id && $character->is_visible) {
                Notifications::create('IMAGE_UPLOAD', $character->user, [
                    'character_url'  => $character->url,
                    'character_slug' => $character->slug,
                    'character_name' => $character->name,
                    'sender_url'     => $user->url,
                    'sender_name'    => $user->name,
                ]);
            }

            // Notify bookmarkers
            $character->notifyBookmarkers('BOOKMARK_IMAGE');

            return $this->commitReturn($character);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character image.
     *
     * @param array          $data
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function updateImageFeatures($data, $image, $user) {
        DB::beginTransaction();

        try {
            // Check that the subtype matches
            if (isset($data['subtype_ids']) && $data['subtype_ids']) {
                if (count($data['subtype_ids']) > config('lorekeeper.extensions.multiple_subtype_limit')) {
                    throw new \Exception('Too many subtypes selected.');
                }

                if (!(isset($data['species_id']) && $data['species_id'])) {
                    throw new \Exception('Species must be selected to select a subtype.');
                }

                $species_id = $data['species_id'] != $image->species_id ? $data['species_id'] : $image->species_id;

                foreach ($data['subtype_ids'] as $subtypeId) {
                    $subtype = Subtype::find($subtypeId);
                    if (!$subtype || $subtype->species_id != $species_id) {
                        throw new \Exception('Selected subtype invalid or does not match species.');
                    }
                }
            }

            if (isset($data['generation_id']) && $data['generation_id']) {
                $generation = CharacterGeneration::find($data['generation_id']);
                if (!$generation) {
                    throw new \Exception('Selected generation is invalid.');
                }
            }

            if ((isset($data['pedigree_id']) && $data['pedigree_id']) || (isset($data['pedigree_descriptor']) && $data['pedigree_descriptor'])) {
                $pedigree = CharacterPedigree::find($data['pedigree_id']);

                if ((!isset($data['pedigree_descriptor']) && $data['pedigree_id']) || (!isset($data['pedigree_id']) && $data['pedigree_descriptor'])) {
                    throw new \Exception('If you are assigning this character a pedigree name, then both pedigree tag and pedigree descriptor must be set.');
                }

                if (!$pedigree) {
                    throw new \Exception('Selected pedigree tag is invalid.');
                }
            }

            if (!$this->logAdminAction($user, 'Updated Image', 'Updated character image features on <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            // Log old features
            $old = [];
            $old['features'] = $this->generateFeatureList($image);
            $old['species'] = $image->species_id ? $image->species->displayName : null;
            $old['subtypes'] = count($image->subtypes) ? $image->displaySubtypes() : null;
            $old['sex'] = $image->sex ? $image->sex : null;

            $old['generation'] = $image->character->generation_id ? $image->character->generation->name : 'No Generation';
            $old['pedigree'] = $image->character->pedigree_id ? $image->character->pedigree->name.' '.$image->character->pedigree_descriptor : 'No Pedigree Name';
            $old['nickname'] = $image->character->nickname ?? 'No Nickname';
            $old['birthdate'] = $image->character->birthdate ?? 'Birthdate Unknown';
            $old['poucher_code'] = $image->character->poucher_code ?? 'No Poucher Code';
            $old['transformation'] = $image->transformation_id ? $image->transformation->displayName : null;

            // Clear old features
            $image->features()->delete();

            // Attach features
            foreach ($data['feature_id'] as $key => $featureId) {
                if ($featureId) {
                    $feature = CharacterFeature::create(['character_image_id' => $image->id, 'feature_id' => $featureId, 'data' => $data['feature_data'][$key]]);
                }
            }

            // Update other stats
            $image->species_id = $data['species_id'];
            // SUBTYPES
            $image->subtypes()->delete();
            if (isset($data['subtype_ids']) && $data['subtype_ids']) {
                if (count($data['subtype_ids']) > config('lorekeeper.extensions.multiple_subtype_limit')) {
                    throw new \Exception('Too many subtypes selected.');
                }
                foreach ($data['subtype_ids'] as $subtypeId) {
                    CharacterImageSubtype::create([
                        'character_image_id' => $image->id,
                        'subtype_id'         => $subtypeId,
                    ]);
                }
            }
            $image->sex = $data['sex'];
            $image->transformation_id = $data['transformation_id'] ?? null;
            $image->save();

            // Update Character Stats
            $image->character->generation_id = $data['generation_id'] ?? null;
            $image->character->pedigree_id = $data['pedigree_id'] ?? null;
            $image->character->pedigree_descriptor = $data['pedigree_descriptor'] ?? null;
            $image->character->nickname = $data['nickname'] ?? null;
            $image->character->birthdate = $data['birthdate'] ?? null;
            $image->character->poucher_code = $data['poucher_code'] ?? null;
            $image->character->save();

            $new = [];
            $new['features'] = $this->generateFeatureList($image);
            $new['species'] = $image->species_id ? $image->species->displayName : null;
            $new['subtypes'] = count($image->subtypes) ? $image->displaySubtypes() : null;
            $new['rarity'] = $image->rarity_id ? $image->rarity->displayName : null;
            $new['sex'] = $image->sex ? $image->sex : null;
            $new['transformation'] = $image->transformation_id ? $image->transformation->displayName : null;

            if (isset($data['generation_id']) && $data['generation_id']) {
                $generation = CharacterGeneration::find($data['generation_id']);
            } else {
                $generation = null;
            }

            if (isset($data['pedigree_id']) && $data['pedigree_id']) {
                $pedigree = CharacterPedigree::find($data['pedigree_id']);
            } else {
                $pedigree = null;
            }

            $new['generation'] = isset($generation) ? $generation->name : 'No Generation';
            $new['pedigree'] = isset($pedigree) ? $pedigree->name.' '.$data['pedigree_descriptor'] : 'No Pedigree Name';
            $new['nickname'] = $data['nickname'] ?? 'No Nickname';
            $new['birthdate'] = $data['birthdate'] ?? 'Birthdate Unknown';
            $new['poucher_code'] = $data['poucher_code'] ?? 'No Poucher Code';

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Traits Updated', '#'.$image->id, 'character', true, $old, $new);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's breeding slot.
     *
     * @param array          $data
     * @param CharacterBreedingSlot $slot
     * @param User           $user
     *
     * @return bool
     */
    public function updateBreedingSlot($data, $slot, $user) {
        DB::beginTransaction();

        try {
            $oldUser = $slot->user_id ? User::find($slot->user_id) : null;
            $recipient = null;
            if (isset($data['user_id']) && $data['user_id']) {
                $recipient = User::find($data['user_id']);
                if (!$recipient) {
                    throw new \Exception('Selected user is invalid.');
                }
                if (!$oldUser || $slot->user_id != $recipient->id) {
                    $userChange = true;
                }
            } elseif (!isset($data['user_id'])) {
                $data['user_id'] = null;
                if ($oldUser) {
                    $userChange = true;
                }
            }

            $oldUrl = $slot->user_url ? prettyProfileLink($slot->user_url) : null;
            $recipientUrl = null;
            if (isset($data['user_url']) && $data['user_url']) {
                $recipientUrl = checkAlias($data['user_url'], false);

                if (is_object($recipientUrl)) {
                    $data['user_id'] = $recipientUrl->id;
                    $data['user_url'] = null;
                    $recipientUrl = null;
                    if (!$oldUser || isset($oldUser->id) && $oldUser->id != $recipientUrl->id) {
                        $userChange = true;
                    }
                } elseif (!$oldUrl || $slot->user_url != $data['user_url']) {
                    $userChange = true;
                }
            } elseif (!isset($data['user_url'])) {
                $data['user_url'] = null;
                if ($oldUrl) {
                    $userChange = true;
                }
            }

            $oldOffspring = $slot->offspring_id ? Character::find($slot->offspring_id) : null;
            $offspring = null;
            if (isset($data['offspring_id']) && $data['offspring_id']) {
                $offspring = Character::find($data['offspring_id']);
                if (!$offspring) {
                    throw new \Exception('Selected user is invalid.');
                }

                $data['offspring_id'] = $offspring->id;
                if (!$oldOffspring || isset($oldOffspring->id) && $oldOffspring->id != $offspring->id) {
                    $offspringChange = true;
                }
            } elseif (!isset($data['offspring_id'])) {
                $data['offspring_id'] = null;
                if ($oldOffspring) {
                    $offspringChange = true;
                }
            }

            if (!isset($data['notes'])) {
                $data['notes'] = null;
            }

            $slot->user_id = $data['user_id'];
            $slot->user_url = $data['user_url'];
            $slot->offspring_id = $data['offspring_id'];
            $slot->notes = $data['notes'];
            $slot->save();

            if (!$this->logAdminAction($user, 'Updated Breeding Slot', 'Updated breeding slot entry for '.$slot->character->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            $string = '';
            if (isset($userChange)) {
                $string .= 'User changed from '.($oldUser->displayName ?? ($oldUrl ? prettyProfileLink($oldUrl) : 'no user')).' to '.($recipient->displayName ?? ($recipientUrl ? prettyProfileLink($recipientUrl) : 'no user')).'.';
            }
            if (isset($offspringChange)) {
                if (isset($userChange)) {
                    $string .= ' ';
                }
                $string .= 'Offspring set to '.(isset($offspring) ? $offspring->displayName : 'none').'.';
            }
            // Add a log for the character
            $this->createLog($user->id, null, null, null, $slot->character_id, 'Breeding Slot Updated', $string, 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }


    /**
     * Updates image data.
     *
     * @param array          $data
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function updateImageNotes($data, $image, $user) {
        DB::beginTransaction();

        try {
            $old = $image->parsed_description;

            // Update the image's notes
            $image->description = $data['description'];
            $image->parsed_description = parse($data['description']);
            $image->save();

            if (!$this->logAdminAction($user, 'Updated Image Notes', 'Updated image <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Notes Updated', '[#'.$image->id.']', 'character', true, $old, $image->parsed_description);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates image credits.
     *
     * @param array          $data
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function updateImageCredits($data, $image, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Image Credits', 'Updated character image credits on <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            $old = $this->generateCredits($image);

            // Clear old artists/designers
            $image->creators()->delete();

            // Check if entered url(s) have aliases associated with any on-site users
            $designers = array_filter($data['designer_url']); // filter null values
            foreach ($designers as $key=> $url) {
                $recipient = checkAlias($url, false);
                if (is_object($recipient)) {
                    $data['designer_id'][$key] = $recipient->id;
                    $designers[$key] = null;
                }
            }
            $artists = array_filter($data['artist_url']);  // filter null values
            foreach ($artists as $key=> $url) {
                $recipient = checkAlias($url, false);
                if (is_object($recipient)) {
                    $data['artist_id'][$key] = $recipient->id;
                    $artists[$key] = null;
                }
            }

            // Check that users with the specified id(s) exist on site
            foreach ($data['designer_id'] as $id) {
                if (isset($id) && $id) {
                    $user = User::find($id);
                    if (!$user) {
                        throw new \Exception('One or more designers is invalid.');
                    }
                }
            }
            foreach ($data['artist_id'] as $id) {
                if (isset($id) && $id) {
                    $user = $user = User::find($id);
                    if (!$user) {
                        throw new \Exception('One or more artists is invalid.');
                    }
                }
            }

            // Attach artists/designers
            foreach ($data['designer_id'] as $key => $id) {
                if ($id || $data['designer_url'][$key]) {
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type'               => 'Designer',
                        'url'                => $data['designer_url'][$key],
                        'user_id'            => $id,
                    ]);
                }
            }
            foreach ($data['artist_id'] as $key => $id) {
                if ($id || $data['artist_url'][$key]) {
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type'               => 'Artist',
                        'url'                => $data['artist_url'][$key],
                        'user_id'            => $id,
                    ]);
                }
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Credits Updated', '[#'.$image->id.']', 'character', true, $old, $this->generateCredits($image));

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Reuploads an image.
     *
     * @param array          $data
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function reuploadImage($data, $image, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Reuploaded Image', 'Reuploaded character image <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
                //Clear out longest side measurement since it might not be accurate anymore
                $image->longest_side = null; // this will get saved via either side of the if check here
            }

            if (config('lorekeeper.settings.masterlist_image_format') != null) {
                // Remove old versions so that images in various filetypes don't pile up
                if (file_exists($image->imagePath.'/'.$image->imageFileName)) {
                    unlink($image->imagePath.'/'.$image->imageFileName);
                }
                if (isset($image->fullsize_hash) ? file_exists(public_path($image->imageDirectory.'/'.$image->fullsizeFileName)) : false) {
                    if (file_exists($image->imagePath.'/'.$image->fullsizeFileName)) {
                        unlink($image->imagePath.'/'.$image->fullsizeFileName);
                    }
                }
                if (file_exists($image->imagePath.'/'.$image->thumbnailFileName)) {
                    unlink($image->imagePath.'/'.$image->thumbnailFileName);
                }

                // Set the image's extension in the DB as defined in settings
                $image->extension = config('lorekeeper.settings.masterlist_image_format');
                $image->save();
            } else {
                // Get uploaded image's extension and save it to the DB
                $image->extension = $data['image']->getClientOriginalExtension();
                $image->save();
            }

            // Save image
            $this->handleImage($data['image'], $image->imageDirectory, $image->imageFileName);

            $isMyo = $image->character->is_myo_slot ? true : false;
            // Save thumbnail
            if (isset($data['use_cropper'])) {
                $this->cropThumbnail(Arr::only($data, ['x0', 'x1', 'y0', 'y1']), $image, $isMyo);
            } else {
                $this->handleImage($data['thumbnail'], $image->thumbnailPath, $image->thumbnailFileName);
            }

            // Process and save the image itself
            if (!$isMyo) {
                $this->processImage($image);
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Reuploaded', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an image.
     *
     * @param CharacterImage $image
     * @param User           $user
     * @param bool           $forceDelete
     *
     * @return bool
     */
    public function deleteImage($image, $user, $forceDelete = false) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Deleted Image', 'Deleted character image <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            if (!$forceDelete && $image->character->character_image_id == $image->id) {
                throw new \Exception("Cannot delete a character's active image.");
            }

            $image->features()->delete();

            $image->delete();

            // Delete the image files
            if (file_exists($image->imagePath.'/'.$image->imageFileName)) {
                unlink($image->imagePath.'/'.$image->imageFileName);
            }
            if (isset($image->fullsize_hash) ? file_exists(public_path($image->imageDirectory.'/'.$image->fullsizeFileName)) : false) {
                if (file_exists($image->imagePath.'/'.$image->fullsizeFileName)) {
                    unlink($image->imagePath.'/'.$image->fullsizeFileName);
                }
            }
            if (file_exists($image->imagePath.'/'.$image->thumbnailFileName)) {
                unlink($image->imagePath.'/'.$image->thumbnailFileName);
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Deleted', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates image settings.
     *
     * @param array          $data
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function updateImageSettings($data, $image, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Image', 'Updated character image settings on <a href="'.$image->character->url.'">#'.$image->id.'</a>')) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image->character->character_image_id == $image->id && !isset($data['is_visible'])) {
                throw new \Exception("Cannot hide a character's active image.");
            }

            $image->is_valid = isset($data['is_valid']);
            $image->is_visible = isset($data['is_visible']);
            $image->content_warnings = isset($data['content_warnings']) ? explode(',', $data['content_warnings']) : null;
            $image->save();

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Visibility/Validity Updated', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's active image.
     *
     * @param CharacterImage $image
     * @param User           $user
     *
     * @return bool
     */
    public function updateActiveImage($image, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Image', 'Set image <a href="'.$image->character->url.'">#'.$image->id.'</a> to active image')) {
                throw new \Exception('Failed to log admin action.');
            }

            if ($image->character->character_image_id == $image->id) {
                return true;
            }
            if (!$image->is_visible) {
                throw new \Exception("Cannot set a non-visible image as the character's active image.");
            }

            $image->character->character_image_id = $image->id;
            $image->character->save();

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Active Image Updated', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts a character's images.
     *
     * @param array $data
     * @param User  $user
     * @param mixed $character
     *
     * @return bool
     */
    public function sortImages($data, $character, $user) {
        DB::beginTransaction();

        try {
            $ids = explode(',', $data['sort']);
            $images = CharacterImage::whereIn('id', $ids)->where('character_id', $character->id)->orderBy(DB::raw('FIELD(id, '.implode(',', $ids).')'))->get();

            if (count($images) != count($ids)) {
                throw new \Exception('Invalid image included in sorting order.');
            }
            if (!$images->first()->is_visible) {
                throw new \Exception("Cannot set a non-visible image as the character's active image.");
            }

            $count = 0;
            foreach ($images as $image) {
                // if($count == 1)
                // {
                //    // Set the first one as the active image
                //    $image->character->image_id = $image->id;
                //    $image->character->save();
                // }
                $image->sort = $count;
                $image->save();
                $count++;
            }

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $image->character_id, 'Image Order Updated', '', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Generates a colour palette based on the image.
     *
     * @param mixed      $character_image
     * @param mixed      $user
     * @param mixed|null $colours
     */
    public function imageColours($character_image, $user, $colours = null) {
        DB::beginTransaction();

        try {
            $created = $colours ? false : true;
            if (!$colours) {
                $palette = Palette::fromFilename($character_image->imagePath.'/'.$character_image->imageFileName);

                $extractor = new ColorExtractor($palette);

                $colours = $extractor->extract(config('lorekeeper.character_pairing.colour_count'));

                foreach ($colours as $key => $colour) {
                    $colours[$key] = Color::fromIntToHex($colour);
                }
            }

            $character_image->colours = json_encode($colours);
            $character_image->save();

            $this->createLog($user->id, null, null, null, $character_image->character_id, 'Image Colours '.($created ? 'Generated' : 'Updated'), '', 'character');

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts a user's characters.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function sortCharacters($data, $user) {
        DB::beginTransaction();

        try {
            $ids = array_reverse(explode(',', $data['sort']));
            $folders = array_reverse($data['folder_ids']);

            $characters = Character::myo(0)->whereIn('id', $ids)->where('user_id', $user->id)->where('is_visible', 1)->orderBy(DB::raw('FIELD(id, '.implode(',', $ids).')'))->get();

            if (count($characters) != count($ids)) {
                throw new \Exception('Invalid character included in sorting order.');
            }

            $count = 0;
            foreach ($characters as $character) {
                $character->sort = $count;
                if ($folders[$count] == 'None') {
                    $character->folder_id = null;
                } else {
                    $character->folder_id = $folders[$count];
                }
                $character->save();
                $count++;
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's genome.
     *
     * @param array           $data
     * @param CharacterGenome $genome
     * @param User            $user
     * @param mixed           $character
     *
     * @return bool
     */
    public function updateCharacterGenome($data, $character, $genome, $user) {
        DB::beginTransaction();
        try {
            if (!$user->hasPower('view_hidden_genetics')) {
                throw new \Exception("You don't have the power to see this.");
            }

            $this->handleCharacterGenome($data, $character, $genome, $user);

            // $character->update();
            $this->createLog($user->id, null, null, null, $character->id, 'Character Updated', 'Genome edited', 'character', true);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts a character's pets.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function sortCharacterPets($data, $user) {
        DB::beginTransaction();

        try {
            $ids = array_reverse(explode(',', $data['sort']));
            $pets = UserPet::whereIn('id', $ids)->where('user_id', $user->id)->orderBy(DB::raw('FIELD(id, '.implode(',', $ids).')'))->get();

            if (count($pets) != count($ids)) {
                throw new \Exception('Invalid pet included in sorting order.');
            }

            $count = 0;
            foreach ($pets as $pet) {
                $pet->sort = $count;
                $pet->save();
                $count++;
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a breeding permission.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     *
     * @return bool
     */
    public function createBreedingPermission($data, $character, $user) {
        DB::beginTransaction();

        try {
            // Perform additional checks
            if ($character->user_id != $user->id) {
                throw new \Exception('Only this character\'s owner may create new breeding permissions.');
            }
            if ($user->id == $data['recipient_id']) {
                throw new \Exception('You cannot grant a breeding permission to yourself.');
            }
            if ($character->availableBreedingPermissions < 1) {
                throw new \Exception('This character may not have any more breeding permissions created.');
            }

            // Create the permission itself
            $permission = BreedingPermission::create([
                'character_id' => $character->id,
                'recipient_id' => $data['recipient_id'],
                'type'         => $data['type'],
                'description'  => $data['description'],
            ]);

            if (!$permission) {
                throw new \Exception('Failed to create breeding permission.');
            }

            // Create a log for the permission
            if (!$this->createBreedingPermissionLog($user->id, $data['recipient_id'], $permission->id, 'Breeding Permission Granted', $data['type'].' Permission Created')) {
                throw new \Exception('Failed to create log.');
            }

            // Create a notification for the recipient
            Notifications::create('BREEDING_PERMISSION_GRANTED', $permission->recipient, [
                'character_name' => $character->name,
                'character_slug' => $character->slug,
                'sender_url'     => $user->url,
                'sender_name'    => $user->name,
                'type'           => strtolower($permission->type),
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Marks a breeding permission as used.
     *
     * @param Character          $character
     * @param BreedingPermission $permission
     * @param User               $user
     *
     * @return bool
     */
    public function useBreedingPermission($character, $permission, $user) {
        DB::beginTransaction();

        try {
            if (!$permission) {
                throw new \Exception('Invalid breeding permission');
            }
            if ($permission->is_used) {
                throw new \Exception('This permission has already been used.');
            }

            // Update the permission
            $permission->update(['is_used' => 1]);

            // Create a log
            if (!$this->createBreedingPermissionLog($user->id, null, $permission->id, 'Breeding Permission Marked Used', null)) {
                throw new \Exception('Failed to create log.');
            }

            // Create notifications for both the character owner and recipient
            foreach ([$character->user, $permission->recipient] as $notificationRecipient) {
                if ($notificationRecipient->id != $user->id) {
                    Notifications::create('BREEDING_PERMISSION_USED', $notificationRecipient, [
                        'character_name' => $character->name,
                        'character_slug' => $character->slug,
                        'sender_url'     => $user->url,
                        'sender_name'    => $user->name,
                        'type'           => strtolower($permission->type),
                        'permission_id'  => $permission->id,
                    ]);
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Transfers a breeding permission.
     *
     * @param Character          $character
     * @param BreedingPermission $permission
     * @param User               $recipient
     * @param User               $user
     *
     * @return bool
     */
    public function transferBreedingPermission($character, $permission, $recipient, $user) {
        DB::beginTransaction();

        try {
            if (!$permission) {
                throw new \Exception('Invalid breeding permission');
            }
            if ($permission->is_used) {
                throw new \Exception('This permission has already been used.');
            }

            if (!$recipient) {
                throw new \Exception('Invalid recipient.');
            }
            if ($recipient->id == $permission->recipient_id) {
                throw new \Exception('Cannot transfer breeding permission; the current and selected recipient are the same.');
            }

            // It might be strange to allow transferral of breeding permissions back
            // to the character's original owner, but it also might come in handy.
            // The following line would disallow this; it is preserved here, albeit commented out, for convenience.
            //if($recipient->id == $character->user_id) throw new \Exception('Cannot transfer breeding permission; the selected recipient is the character\'s owner.');

            // Record the pre-existing recipient
            $oldRecipient = $permission->recipient;

            // Update the permission
            $permission->update(['recipient_id' => $recipient->id]);

            // Create a log
            if (!$this->createBreedingPermissionLog($oldRecipient->id, $recipient->id, $permission->id, 'Breeding Permission Transferred', 'Transferred by '.$user->displayName.($user->id != $oldRecipient->id ? ' (Admin Transfer)' : ''))) {
                throw new \Exception('Failed to create log.');
            }

            // If this is a forced/admin transfer, send the original recipient a notification
            if ($user->id != $oldRecipient->id) {
                Notifications::create('FORCED_BREEDING_PERMISSION_TRANSFER', $oldRecipient, [
                    'character_name' => $character->name,
                    'character_slug' => $character->slug,
                    'sender_url'     => $user->url,
                    'sender_name'    => $user->name,
                    'type'           => strtolower($permission->type),
                ]);
            }

            // Create a notification for the recipient
            if ($recipient->id != $user->id) {
                Notifications::create('BREEDING_PERMISSION_TRANSFER', $recipient, [
                    'character_name' => $character->name,
                    'character_slug' => $character->slug,
                    'sender_url'     => $user->url,
                    'sender_name'    => $user->name,
                    'type'           => strtolower($permission->type),
                ]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a breeding permission log.
     *
     * @param int    $senderId
     * @param int    $recipientId
     * @param int    $breedingPermissionId
     * @param string $type
     * @param string $data
     *
     * @return bool
     */
    public function createBreedingPermissionLog($senderId, $recipientId, $breedingPermissionId, $type, $data) {
        DB::beginTransaction();

        try {
            BreedingPermissionLog::create([
                'sender_id'              => $senderId,
                'recipient_id'           => $recipientId,
                'breeding_permission_id' => $breedingPermissionId,
                'log'                    => $type.($data ? ' ('.$data.')' : ''),
                'log_type'               => $type,
                'data'                   => $data,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's stats.
     *
     * @param array $data
     * @param User  $user
     * @param mixed $character
     *
     * @return bool
     */
    public function updateCharacterStats($data, $character, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Stats', 'Updated character stats on '.$character->displayname)) {
                throw new \Exception('Failed to log admin action.');
            }

            if (!$character->is_myo_slot && Character::where('slug', $data['slug'])->where('id', '!=', $character->id)->exists()) {
                throw new \Exception('Character code must be unique.');
            }

            if (isset($data['rarity_id']) && $data['rarity_id']) {
                $rarity = Rarity::find($data['rarity_id']);
                if (!$rarity) {
                    throw new \Exception('Selected rarity is invalid.');
                }
            }

            $characterData = Arr::only($data, [
                'character_category_id',
                'number', 'slug', 'rarity_id',
            ]);
            $characterData['is_sellable'] = isset($data['is_sellable']);
            $characterData['is_tradeable'] = isset($data['is_tradeable']);
            $characterData['is_giftable'] = isset($data['is_giftable']);
            $characterData['rarity_id'] = $data['rarity_id'] ?? $character->rarity_id;
            $characterData['sale_value'] = $data['sale_value'] ?? 0;
            $characterData['transferrable_at'] = $data['transferrable_at'] ?? null;
            if ($character->is_myo_slot) {
                $characterData['name'] = (isset($data['name']) && $data['name']) ? $data['name'] : null;
            }

            // Needs to be cleaned up
            $result = [];
            $old = [];
            $new = [];
            if (!$character->is_myo_slot) {
                if ($characterData['character_category_id'] != $character->character_category_id) {
                    $result[] = 'character category';
                    $old['character_category'] = $character->category->displayName;
                    $new['character_category'] = CharacterCategory::find($characterData['character_category_id'])->displayName;
                }
                if ($characterData['number'] != $character->number) {
                    $result[] = 'character number';
                    $old['number'] = $character->number;
                    $new['number'] = $characterData['number'];
                }
                if ($characterData['slug'] != $character->slug) {
                    $result[] = 'character code';
                    $old['slug'] = $character->slug;
                    $new['slug'] = $characterData['slug'];
                }
                if ($characterData['rarity_id'] != $character->rarity_id) {
                    $result[] = 'rarity';
                    $old['rarity'] = $character->rarity_id ? $character->rarity->displayName : 'No Rarity';
                    $new['rarity'] = Rarity::find($characterData['rarity_id'])->displayName;

                    $character->rarity_id = $characterData['rarity_id'];
                    $character->save();

                    $character->image->rarity_id = $characterData['rarity_id'];
                    $character->image->save();
                }
            } else {
                if ($characterData['name'] != $character->name) {
                    $result[] = 'name';
                    $old['name'] = $character->name;
                    $new['name'] = $characterData['name'];
                }
            }

            if ($characterData['is_sellable'] != $character->is_sellable) {
                $result[] = 'sellable status';
                $old['is_sellable'] = $character->is_sellable;
                $new['is_sellable'] = $characterData['is_sellable'];
            }
            if ($characterData['is_tradeable'] != $character->is_tradeable) {
                $result[] = 'tradeable status';
                $old['is_tradeable'] = $character->is_tradeable;
                $new['is_tradeable'] = $characterData['is_tradeable'];
            }
            if ($characterData['is_giftable'] != $character->is_giftable) {
                $result[] = 'giftable status';
                $old['is_giftable'] = $character->is_giftable;
                $new['is_giftable'] = $characterData['is_giftable'];
            }
            if ($characterData['sale_value'] != $character->sale_value) {
                $result[] = 'sale value';
                $old['sale_value'] = $character->sale_value;
                $new['sale_value'] = $characterData['sale_value'];
            }
            if ($characterData['transferrable_at'] != $character->transferrable_at) {
                $result[] = 'transfer cooldown';
                $old['transferrable_at'] = $character->transferrable_at;
                $new['transferrable_at'] = $characterData['transferrable_at'];
            }

            if (count($result)) {
                $character->update($characterData);

                // Add a log for the character
                // This logs all the updates made to the character
                $this->createLog($user->id, null, null, null, $character->id, 'Character Updated', ucfirst(implode(', ', $result)).' edited', 'character', true, $old, $new);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's description.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     *
     * @return bool
     */
    public function updateCharacterDescription($data, $character, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Character Description', 'Updated character description on '.$character->displayname)) {
                throw new \Exception('Failed to log admin action.');
            }

            $old = $character->parsed_description;

            // Update the image's notes
            $character->description = $data['description'];
            $character->parsed_description = parse($data['description']);
            $character->save();

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $character->id, 'Character Description Updated', '', 'character', true, $old, $character->parsed_description);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's settings.
     *
     * @param array $data
     * @param User  $user
     * @param mixed $character
     *
     * @return bool
     */
    public function updateCharacterSettings($data, $character, $user) {
        DB::beginTransaction();

        try {
            if (!$this->logAdminAction($user, 'Updated Character Settings', 'Updated character settings on '.$character->displayname)) {
                throw new \Exception('Failed to log admin action.');
            }

            $old = [
                'is_visible' => $character->is_visible,
            ];

            $character->is_visible = isset($data['is_visible']);
            $character->save();

            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, null, $character->id, 'Character Settings Updated', '', 'character', true, $old, [
                'is_visible' => $character->is_visible,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a character's profile.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     * @param bool      $isAdmin
     *
     * @return bool
     */
    public function updateCharacterProfile($data, $character, $user, $isAdmin = false) {
        DB::beginTransaction();

        try {
            $notifyTrading = false;
            $notifyGiftArt = false;
            $notifyGiftWriting = false;

            // Allow updating the gift art/trading options if the editing
            // user owns the character
            if (!$isAdmin) {
                if ($character->user_id != $user->id) {
                    throw new \Exception('You cannot edit this character.');
                }

                if ($character->is_trading != isset($data['is_trading'])) {
                    $notifyTrading = true;
                }
                if (isset($data['is_gift_art_allowed']) && $character->is_gift_art_allowed != $data['is_gift_art_allowed']) {
                    $notifyGiftArt = true;
                }
                if (isset($data['is_gift_writing_allowed']) && $character->is_gift_writing_allowed != $data['is_gift_writing_allowed']) {
                    $notifyGiftWriting = true;
                }
                if (!isset($data['is_links_open'])) {
                    $data['is_links_open'] = 0;
                }

                $character->is_gift_art_allowed = isset($data['is_gift_art_allowed']) && $data['is_gift_art_allowed'] <= 2 ? $data['is_gift_art_allowed'] : 0;
                $character->is_gift_writing_allowed = isset($data['is_gift_writing_allowed']) && $data['is_gift_writing_allowed'] <= 2 ? $data['is_gift_writing_allowed'] : 0;
                $character->is_trading = isset($data['is_trading']);
                $character->is_links_open = $data['is_links_open'];
                $character->save();
            } else {
                if (!$this->logAdminAction($user, 'Updated Character Profile', 'Updated character profile on '.$character->displayname)) {
                    throw new \Exception('Failed to log admin action.');
                }
            }

            // Update the character's profile
            if (!$character->is_myo_slot) {
                $character->name = $data['name'];
            }
            $character->save();

            if (!$character->is_myo_slot && config('lorekeeper.extensions.character_TH_profile_link')) {
                $character->profile->link = $data['link'];
            }
            $character->profile->save();

            $character->profile->text = $data['text'];
            $character->profile->parsed_text = parse($data['text']);
            $character->profile->save();

            if (!$character->is_myo_slot) {
                // clear old custom values and add new ones.
                $character->profile->custom_values()->delete();
                if (isset($data['custom_values_data'])) {
                    foreach ($data['custom_values_data'] as $i => $val) {
                        $val_parsed = parse($val);
                        if ($val_parsed != '') {
                            $group = isset($data['custom_values_group']) ? $data['custom_values_group'][$i] : null;
                            $name = isset($data['custom_values_name']) ? $data['custom_values_name'][$i] : null;
                            $custom_value = CharacterProfileCustomValue::create([
                                'character_id' => $character->id,
                                'group'        => $group,
                                'name'         => $name,
                                'data'         => $val,
                                'data_parsed'  => $val_parsed,
                            ]);
                        }
                    }
                }
                $character->profile->save();
            }

            if ($isAdmin && isset($data['alert_user']) && $character->is_visible && $character->user_id) {
                Notifications::create('CHARACTER_PROFILE_EDIT', $character->user, [
                    'character_name' => $character->name,
                    'character_slug' => $character->is_myo_slot ? $character->id : $character->slug,
                    'sender_url'     => $user->url,
                    'sender_name'    => $user->name,
                ]);
            }

            if ($notifyTrading) {
                $character->notifyBookmarkers('BOOKMARK_TRADING');
            }
            if ($notifyGiftArt) {
                $character->notifyBookmarkers('BOOKMARK_GIFTS');
            }
            if ($notifyGiftWriting) {
                $character->notifyBookmarkers('BOOKMARK_GIFT_WRITING');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a character.
     *
     * @param Character $character
     * @param User      $user
     *
     * @return bool
     */
    public function deleteCharacter($character, $user) {
        DB::beginTransaction();

        try {
            if (SalesCharacter::where('character_id', $character->id)->exists()) {
                throw new \Exception('This character currently exists in a previous sale post and cannot be deleted.');
            }
            if ($character->user_id) {
                $character->user->settings->save();
            }

            if (!$this->logAdminAction($user, 'Deleted Character', 'Deleted character '.$character->slug)) {
                throw new \Exception('Failed to log admin action.');
            }

            // Delete associated bookmarks
            CharacterBookmark::where('character_id', $character->id)->delete();

            // Delete associated features and images
            // Images use soft deletes
            foreach ($character->images as $image) {
                $image->features()->delete();
                $image->delete();
            }

            // Delete associated currencies
            CharacterCurrency::where('character_id', $character->id)->delete();

            // Delete associated design updates
            // Design updates use soft deletes
            CharacterDesignUpdate::where('character_id', $character->id)->delete();

            // Delete character
            // This is a soft delete, so the character still kind of exists
            $character->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a character transfer.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     *
     * @return bool
     */
    public function createTransfer($data, $character, $user) {
        DB::beginTransaction();

        try {
            if ($user->id != $character->user_id) {
                throw new \Exception('You do not own this character.');
            }
            if (!$character->is_sellable && !$character->is_tradeable && !$character->is_giftable) {
                throw new \Exception('This character is not transferrable.');
            }
            if ($character->transferrable_at && $character->transferrable_at->isFuture()) {
                throw new \Exception('This character is still on transfer cooldown and cannot be transferred.');
            }
            if (CharacterTransfer::active()->where('character_id', $character->id)->exists()) {
                throw new \Exception('This character is in an active transfer.');
            }
            if ($character->trade_id) {
                throw new \Exception('This character is in an active trade.');
            }

            $recipient = User::find($data['recipient_id']);
            if (!$recipient) {
                throw new \Exception('Invalid user selected.');
            }
            if ($recipient->is_banned) {
                throw new \Exception('Cannot transfer character to a banned member.');
            }

            // deletes any pending design drafts
            foreach ($character->designUpdate as $update) {
                if ($update->status == 'Draft') {
                    if (!(new DesignUpdateManager)->rejectRequest('Cancelled by '.$user->displayName.' in order to transfer character to another user', $update, $user, true, false)) {
                        throw new \Exception('Could not cancel pending request.');
                    }
                }
            }

            $queueOpen = Settings::get('open_transfers_queue');

            CharacterTransfer::create([
                'user_reason'  => $data['user_reason'],  // pulls from this characters user_reason collum
                'character_id' => $character->id,
                'sender_id'    => $user->id,
                'recipient_id' => $recipient->id,
                'status'       => 'Pending',

                // if the queue is closed, all transfers are auto-approved
                'is_approved'  => !$queueOpen,
            ]);

            if (!$queueOpen) {
                Notifications::create('CHARACTER_TRANSFER_RECEIVED', $recipient, [
                    'character_url'  => $character->url,
                    'character_name' => $character->slug,
                    'sender_name'    => $user->name,
                    'sender_url'     => $user->url,
                ]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Forces an admin transfer of a character.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     *
     * @return bool
     */
    public function adminTransfer($data, $character, $user) {
        DB::beginTransaction();

        try {
            if (isset($data['recipient_id']) && $data['recipient_id']) {
                $recipient = User::find($data['recipient_id']);
                if (!$recipient) {
                    throw new \Exception('Invalid user selected.');
                }
                if ($character->user_id == $recipient->id) {
                    throw new \Exception('Cannot transfer a character to the same user.');
                }
                if (!$this->logAdminAction($user, 'Admin Transfer', 'Admin transferred '.$character->displayname.' to '.$recipient->displayName)) {
                    throw new \Exception('Failed to log admin action.');
                }
            } elseif (isset($data['recipient_url']) && $data['recipient_url']) {
                // Transferring to an off-site user
                $recipient = checkAlias($data['recipient_url'], false);
                if (!$this->logAdminAction($user, 'Admin Transfer', 'Admin transferred '.$character->displayname.' to '.$recipient)) {
                    throw new \Exception('Failed to log admin action.');
                }
            } else {
                throw new \Exception('Please enter a recipient for the transfer.');
            }

            // If the character is in an active transfer, cancel it
            $transfer = CharacterTransfer::active()->where('character_id', $character->id)->first();
            if ($transfer) {
                $transfer->status = 'Canceled';
                $transfer->reason = 'Transfer canceled by '.$user->displayName.' in order to transfer character to another user';
                $transfer->save();
            }
            // deletes any pending design drafts
            foreach ($character->designUpdate as $update) {
                if ($update->status == 'Draft') {
                    if (!(new DesignUpdateManager)->rejectRequest('Cancelled by '.$user->displayName.' in order to transfer character to another user', $update, $user, true, false)) {
                        throw new \Exception('Could not cancel pending request.');
                    }
                }
            }

            $sender = $character->user;

            $this->moveCharacter($character, $recipient, 'Transferred by '.$user->displayName.(isset($data['reason']) ? ': '.$data['reason'] : ''), $data['cooldown'] ?? -1);

            // Add notifications for the old and new owners
            if ($sender) {
                Notifications::create('CHARACTER_SENT', $sender, [
                    'character_name' => $character->slug,
                    'character_url'  => $character->is_myo_slot ? 'myo/'.$character->id : 'character/'.$character->slug,
                    'sender_name'    => $user->name,
                    'sender_url'     => $user->url,
                    'recipient_name' => is_object($recipient) ? $recipient->name : prettyProfileName($recipient),
                    'recipient_url'  => is_object($recipient) ? $recipient->url : $recipient,
                ]);
            }
            if (is_object($recipient)) {
                Notifications::create('CHARACTER_RECEIVED', $recipient, [
                    'character_name' => $character->slug,
                    'character_url'  => $character->is_myo_slot ? 'myo/'.$character->id : 'character/'.$character->slug,
                    'sender_name'    => $user->name,
                    'sender_url'     => $user->url,
                ]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes a character transfer.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function processTransfer($data, $user) {
        DB::beginTransaction();

        try {
            $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->where('recipient_id', $user->id)->first();
            if (!$transfer) {
                throw new \Exception('Invalid transfer selected.');
            }

            if ($data['action'] == 'Accept') {
                $cooldown = Settings::get('transfer_cooldown');

                $transfer->status = 'Accepted';

                // Process the character move if the transfer has already been approved
                if ($transfer->is_approved) {
                    // check the cooldown saved
                    if (isset($transfer->data['cooldown'])) {
                        $cooldown = $transfer->data['cooldown'];
                    }
                    $this->moveCharacter($transfer->character, $transfer->recipient, 'User Transfer', $cooldown);
                    if (!Settings::get('open_transfers_queue')) {
                        $transfer->data = [
                            'cooldown' => $cooldown,
                            'staff_id' => null,
                        ];
                    }

                    // Notify sender of the successful transfer
                    Notifications::create('CHARACTER_TRANSFER_ACCEPTED', $transfer->sender, [
                        'character_name' => $transfer->character->slug,
                        'character_url'  => $transfer->character->url,
                        'sender_name'    => $transfer->recipient->name,
                        'sender_url'     => $transfer->recipient->url,
                    ]);
                }
            } else {
                $transfer->status = 'Rejected';
                $transfer->data = [
                    'staff_id' => null,
                ];

                // Notify sender that transfer has been rejected
                Notifications::create('CHARACTER_TRANSFER_REJECTED', $transfer->sender, [
                    'character_name' => $transfer->character->slug,
                    'character_url'  => $transfer->character->url,
                    'sender_name'    => $transfer->recipient->name,
                    'sender_url'     => $transfer->recipient->url,
                ]);
            }
            $transfer->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Cancels a character transfer.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function cancelTransfer($data, $user) {
        DB::beginTransaction();

        try {
            $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->where('sender_id', $user->id)->first();
            if (!$transfer) {
                throw new \Exception('Invalid transfer selected.');
            }

            $transfer->status = 'Canceled';
            $transfer->save();

            // Notify recipient of the cancelled transfer
            Notifications::create('CHARACTER_TRANSFER_CANCELED', $transfer->recipient, [
                'character_name' => $transfer->character->slug,
                'character_url'  => $transfer->character->url,
                'sender_name'    => $transfer->sender->name,
                'sender_url'     => $transfer->sender->url,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes a character transfer in the approvals queue.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function processTransferQueue($data, $user) {
        DB::beginTransaction();

        try {
            if (isset($data['transfer_id'])) {
                $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->first();
            } else {
                $transfer = $data['transfer'];
            }
            if (!$transfer) {
                throw new \Exception('Invalid transfer selected.');
            }

            if ($data['action'] == 'Approve') {
                $transfer->is_approved = 1;
                $transfer->data = [
                    'staff_id' => $user->id,
                    'cooldown' => $data['cooldown'] ?? Settings::get('transfer_cooldown'),
                ];

                // Process the character move if the recipient has already accepted the transfer
                if ($transfer->status == 'Accepted') {
                    if (!$this->logAdminAction($user, 'Approved Transfer', 'Approved transfer of '.$transfer->character->displayname.' to '.$transfer->recipient->displayname)) {
                        throw new \Exception('Failed to log admin action.');
                    }
                    $this->moveCharacter($transfer->character, $transfer->recipient, 'User Transfer', $data['cooldown'] ?? -1);

                    // Notify both parties of the successful transfer
                    Notifications::create('CHARACTER_TRANSFER_APPROVED', $transfer->sender, [
                        'character_name' => $transfer->character->slug,
                        'character_url'  => $transfer->character->url,
                        'sender_name'    => $user->name,
                        'sender_url'     => $user->url,
                    ]);
                    Notifications::create('CHARACTER_TRANSFER_APPROVED', $transfer->recipient, [
                        'character_name' => $transfer->character->slug,
                        'character_url'  => $transfer->character->url,
                        'sender_name'    => $user->name,
                        'sender_url'     => $user->url,
                    ]);
                } else {
                    if (!$this->logAdminAction($user, 'Approved Transfer', 'Approved transfer of '.$transfer->character->displayname.' to '.$transfer->recipient->displayname)) {
                        throw new \Exception('Failed to log admin action.');
                    }

                    // Still pending a response from the recipient
                    Notifications::create('CHARACTER_TRANSFER_ACCEPTABLE', $transfer->recipient, [
                        'character_name' => $transfer->character->slug,
                        'character_url'  => $transfer->character->url,
                        'sender_name'    => $user->name,
                        'sender_url'     => $user->url,
                    ]);
                }
            } else {
                if (!$this->logAdminAction($user, 'Rejected Transfer', 'Rejected transfer of '.$transfer->character->displayname.' to '.$transfer->recipient->displayname)) {
                    throw new \Exception('Failed to log admin action.');
                }

                $transfer->status = 'Rejected';
                $transfer->reason = $data['reason'] ?? null;
                $transfer->data = [
                    'staff_id' => $user->id,
                ];

                // Notify both parties that the request was denied
                Notifications::create('CHARACTER_TRANSFER_DENIED', $transfer->sender, [
                    'character_name' => $transfer->character->slug,
                    'character_url'  => $transfer->character->url,
                    'sender_name'    => $user->name,
                    'sender_url'     => $user->url,
                ]);
                Notifications::create('CHARACTER_TRANSFER_DENIED', $transfer->recipient, [
                    'character_name' => $transfer->character->slug,
                    'character_url'  => $transfer->character->url,
                    'sender_name'    => $user->name,
                    'sender_url'     => $user->url,
                ]);
            }
            $transfer->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Moves a character from one user to another.
     *
     * @param Character $character
     * @param User      $recipient
     * @param string    $data
     * @param int       $cooldown
     * @param string    $logType
     */
    public function moveCharacter($character, $recipient, $data, $cooldown = -1, $logType = null) {
        if ($character->folder_id) {
            $character->folder_id = null;
            $character->save();
        }

        $sender = $character->user;
        if (!$sender) {
            $sender = $character->owner_url;
        }

        // Update character counts if the sender has an account
        if (is_object($sender)) {
            $sender->settings->save();
        }

        if (is_object($recipient)) {
            if (!$character->is_myo_slot) {
                $recipient->settings->is_fto = 0;
            }
            $recipient->settings->save();
        }

        // Update character owner, sort order and cooldown
        $character->sort = 0;
        if (is_object($recipient)) {
            $character->user_id = $recipient->id;
            $character->owner_url = null;
        } else {
            $character->owner_url = $recipient;
            $character->user_id = null;
        }
        if ($cooldown < 0) {
            // Add the default amount from settings
            $cooldown = Settings::get('transfer_cooldown');
        }
        if ($cooldown > 0) {
            if ($character->transferrable_at && $character->transferrable_at->isFuture()) {
                $character->transferrable_at->addDays($cooldown);
            } else {
                $character->transferrable_at = Carbon::now()->addDays($cooldown);
            }
        }
        $character->save();

        // Notify bookmarkers
        $character->notifyBookmarkers('BOOKMARK_OWNER');

        if (config('lorekeeper.settings.reset_character_status_on_transfer')) {
            // Reset trading status, gift art status, and writing status
            $character->update([
                'is_gift_art_allowed'     => 0,
                'is_gift_writing_allowed' => 0,
                'is_trading'              => 0,
            ]);
        }

        if (config('lorekeeper.settings.reset_character_profile_on_transfer') && !$character->is_myo_slot) {
            // Reset name and profile
            $character->update(['name' => null]);

            // Reset profile
            $character->profile->update([
                'text'        => null,
                'parsed_text' => null,
            ]);
        }

        // Add a log for the ownership change
        $this->createLog(
            is_object($sender) ? $sender->id : null,
            is_object($sender) ? null : $sender,
            $recipient && is_object($recipient) ? $recipient->id : null,
            $recipient && is_object($recipient) ? $recipient->url : ($recipient ?: null),
            $character->id,
            $logType ? $logType : ($character->is_myo_slot ? 'MYO Slot Transferred' : 'Character Transferred'),
            $data,
            'user'
        );
    }

    /**
     * Updates a character's lineage.
     *
     * @param array     $data
     * @param Character $character
     * @param User      $user
     * @param bool      $isAdmin
     *
     * @return bool
     */
    public function updateCharacterLineage($data, $character, $user, $isAdmin = false) {
        DB::beginTransaction();

        try {
            if (!$user->hasPower('manage_characters')) {
                throw new \Exception('You do not have the required permissions to do this.');
            }

            if (!$character->lineage) {
                return $this->handleCharacterLineage($data, $character);
            } else {
                $character->lineage->update([
                    'parent_1_id'   => $data['parent_1_id'] ?? null,
                    'parent_1_name' => $data['parent_1_id'] ? null : ($data['parent_1_name'] ?? null),
                    'parent_2_id'   => $data['parent_2_id'] ?? null,
                    'parent_2_name' => $data['parent_2_id'] ? null : ($data['parent_2_name'] ?? null),
                    'depth'         => $data['depth'] ?? 0,
                ]);
            }
            // CUSTOM ANCESTRY - TODO

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a character genome.
     *
     * @param Character                    $character
     * @param \App\Models\Character\Genome $character
     * @param mixed                        $genome
     *
     * @return bool
     */
    public function deleteCharacterGenome($character, $genome) {
        DB::beginTransaction();
        try {
            if ($genome->character->id != $character->id) {
                throw new \Exception('Wrong character.');
            }

            $genome->genes()->delete();
            $genome->gradients()->delete();
            $genome->numerics()->delete();
            $genome->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Handles character data.
     *
     * @param array $data
     * @param bool  $isMyo
     *
     * @return bool|Character
     */
    private function handleCharacter($data, $isMyo = false) {
        try {
            if ($isMyo) {
                $data['character_category_id'] = null;
                $data['number'] = null;
                $data['slug'] = null;
                $data['species_id'] = isset($data['species_id']) && $data['species_id'] ? $data['species_id'] : null;
                $data['subtype_ids'] = isset($data['subtype_ids']) && $data['subtype_ids'] ? $data['subtype_ids'] : null;
                $data['rarity_id'] = isset($data['rarity_id']) && $data['rarity_id'] ? $data['rarity_id'] : null;
            } else {
                $data['generation_id'] = isset($data['generation_id']) && $data['generation_id'] ? $data['generation_id'] : null;
                $data['pedigree_id'] = isset($data['pedigree_id']) && $data['pedigree_id'] ? $data['pedigree_id'] : null;
                $data['pedigree_descriptor'] = isset($data['pedigree_descriptor']) && $data['pedigree_descriptor'] ? $data['pedigree_descriptor'] : null;
                $data['nickname'] = isset($data['nickname']) && $data['nickname'] ? $data['nickname'] : null;
                $data['birthdate'] = isset($data['birthdate']) && $data['birthdate'] ? $data['birthdate'] : null;
                $data['poucher_code'] = isset($data['poucher_code']) && $data['poucher_code'] ? $data['poucher_code'] : null;
                $data['transformation_id'] = isset($data['transformation_id']) && $data['transformation_id'] ? $data['transformation_id'] : null;
            }

            $characterData = Arr::only($data, [
                'character_category_id', 'rarity_id', 'user_id',
                'number', 'slug', 'description',
                'sale_value', 'transferrable_at', 'is_visible',
                'generation_id', 'pedigree_id', 'pedigree_descriptor',
                'nickname', 'birthdate', 'poucher_code',
            ]);

            $characterData['name'] = ($isMyo && isset($data['name'])) ? $data['name'] : null;
            $characterData['owner_url'] = isset($characterData['user_id']) ? null : $data['owner_url'];
            $characterData['is_sellable'] = isset($data['is_sellable']);
            $characterData['is_tradeable'] = isset($data['is_tradeable']);
            $characterData['is_giftable'] = isset($data['is_giftable']);
            $characterData['is_visible'] = isset($data['is_visible']);
            $characterData['sale_value'] = $data['sale_value'] ?? 0;
            $characterData['is_gift_art_allowed'] = 0;
            $characterData['is_gift_writing_allowed'] = 0;
            $characterData['is_trading'] = 0;
            $characterData['parsed_description'] = parse($data['description']);
            if ($isMyo) {
                $characterData['is_myo_slot'] = 1;
            }

            $character = Character::create($characterData);

            // Create character profile row
            $character->profile()->create([]);

            return $character;
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return false;
    }

    /**
     * Handles character genome data.
     *
     * @param array           $data
     * @param Character       $character
     * @param CharacterGenome $character
     * @param mixed|null      $genome
     *
     * @return bool|CharacterGenome
     */
    private function handleCharacterGenome($data, $character, $genome = null) {
        try {
            if (!$genome) {
                $genome = CharacterGenome::create(['character_id' => $character->id]);
            } else {
                $genome->genes()->delete();
                $genome->gradients()->delete();
                $genome->numerics()->delete();
            }

            $alleleOffset = 0;
            $gradientOffset = 0;
            $numOffset = 0;

            foreach ($data['gene_id'] as $index => $id) {
                $loci = Loci::where('id', $id)->first();
                if ($loci && $loci->type == 'gene') {
                    for ($i = 0; $i < $loci->length; $i++) {
                        $key = $alleleOffset;
                        $allele = $data['gene_allele_id'][$key] ?? null;
                        if ($allele != null) {
                            CharacterGenomeGene::create([
                                'character_genome_id' => $genome->id,
                                'loci_allele_id'      => $allele,
                                'loci_id'             => $loci->id,
                            ]);
                        }
                        $alleleOffset++;
                    }
                } elseif ($loci && $loci->type == 'gradient') {
                    $key = $gradientOffset;
                    $value = $data['gene_gradient_data'][$key] ?? null;
                    $value = preg_replace(["/\+/", '/-/'], ['1', '0'], $value);
                    while (strlen($value) < $loci->length) {
                        $value .= '0';
                    }
                    if ($value != null) {
                        CharacterGenomeGradient::create([
                            'character_genome_id' => $genome->id,
                            'loci_id'             => $loci->id,
                            'value'               => $value,
                        ]);
                    }
                    $gradientOffset++;
                } elseif ($loci && $loci->type == 'numeric') {
                    $key = $numOffset;
                    $value = $data['gene_numeric_data'][$key] ?? null;
                    $value = max(min(255, $value), 0);
                    if ($value != null) {
                        CharacterGenomeNumeric::create([
                            'character_genome_id' => $genome->id,
                            'loci_id'             => $loci->id,
                            'value'               => $value,
                        ]);
                    }
                    $numOffset++;
                }
            }

            if (isset($data['genome_visibility'])) {
                $genome->visibility_level = min(2, max(0, $data['genome_visibility']));
                $genome->save();
            }

            return $genome;
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return false;
    }

    /**
     * Handles character image data.
     *
     * @param array $data
     * @param bool  $isMyo
     * @param mixed $character
     *
     * @return Character           $character
     * @return bool|CharacterImage
     */
    private function handleCharacterImage($data, $character, $isMyo = false) {
        try {
            if ($isMyo) {
                $data['species_id'] = isset($data['species_id']) && $data['species_id'] ? $data['species_id'] : null;
                $data['rarity_id'] = isset($data['rarity_id']) && $data['rarity_id'] ? $data['rarity_id'] : null;
                $data['transformation_id'] = isset($data['transformation_id']) && $data['transformation_id'] ? $data['transformation_id'] : null;

                // Use default images for MYO slots without an image provided
                if (!isset($data['image'])) {
                    $data['image'] = public_path('images/myo.png');
                    $data['thumbnail'] = public_path('images/myo-th.png');
                    $data['extension'] = config('lorekeeper.settings.masterlist_image_format') ?? 'png';
                    $data['fullsize_extension'] = config('lorekeeper.settings.masterlist_fullsizes_format') ?? $data['extension'];
                    $data['default_image'] = true;
                    unset($data['use_cropper']);
                }
            }
            $imageData = Arr::only($data, [
                'species_id', 'rarity_id', 'use_cropper', 'transformation_id',
                'x0', 'x1', 'y0', 'y1', 'content_warnings',
            ]);
            $imageData['use_cropper'] = isset($data['use_cropper']);
            $imageData['description'] = $data['image_description'] ?? null;
            $imageData['parsed_description'] = parse($imageData['description']);
            $imageData['hash'] = randomString(10);
            $imageData['fullsize_hash'] = randomString(15);
            $imageData['sort'] = 0;
            $imageData['is_valid'] = isset($data['is_valid']);
            $imageData['is_visible'] = isset($data['is_visible']);
            $imageData['extension'] = (config('lorekeeper.settings.masterlist_image_format') ?? ($data['extension'] ?? $data['image']->getClientOriginalExtension()));
            $imageData['fullsize_extension'] = (config('lorekeeper.settings.masterlist_fullsizes_format') ?? ($data['fullsize_extension'] ?? $data['image']->getClientOriginalExtension()));
            $imageData['character_id'] = $character->id;
            $imageData['content_warnings'] = isset($data['content_warnings']) ? explode(',', $data['content_warnings']) : null;
            $imageData['sex'] = $data['sex'] ?? null;

            $image = CharacterImage::create($imageData);

            // create subtype relations
            if (isset($data['subtype_ids']) && $data['subtype_ids']) {
                foreach ($data['subtype_ids'] as $subtypeId) {
                    CharacterImageSubtype::create([
                        'character_image_id' => $image->id,
                        'subtype_id'         => $subtypeId,
                    ]);
                }
            }

            // Check if entered url(s) have aliases associated with any on-site users
            $designers = array_filter($data['designer_url']); // filter null values
            foreach ($designers as $key=> $url) {
                $recipient = checkAlias($url, false);
                if (is_object($recipient)) {
                    $data['designer_id'][$key] = $recipient->id;
                    $designers[$key] = null;
                }
            }
            $artists = array_filter($data['artist_url']);  // filter null values
            foreach ($artists as $key=> $url) {
                $recipient = checkAlias($url, false);
                if (is_object($recipient)) {
                    $data['artist_id'][$key] = $recipient->id;
                    $artists[$key] = null;
                }
            }
            // Check that users with the specified id(s) exist on site
            foreach ($data['designer_id'] as $id) {
                if (isset($id) && $id) {
                    $user = User::find($id);
                    if (!$user) {
                        throw new \Exception('One or more designers is invalid.');
                    }
                }
            }
            foreach ($data['artist_id'] as $id) {
                if (isset($id) && $id) {
                    $user = $user = User::find($id);
                    if (!$user) {
                        throw new \Exception('One or more artists is invalid.');
                    }
                }
            }

            // Attach artists/designers
            foreach ($data['designer_id'] as $key => $id) {
                if ($id || $data['designer_url'][$key]) {
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type'               => 'Designer',
                        'url'                => $data['designer_url'][$key],
                        'user_id'            => $id,
                    ]);
                }
            }
            foreach ($data['artist_id'] as $key => $id) {
                if ($id || $data['artist_url'][$key]) {
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type'               => 'Artist',
                        'url'                => $data['artist_url'][$key],
                        'user_id'            => $id,
                    ]);
                }
            }

            // Save image
            $this->handleImage($data['image'], $image->imageDirectory, $image->imageFileName, null, isset($data['default_image']));

            // Save thumbnail first before processing full image
            if (isset($data['use_cropper'])) {
                $this->cropThumbnail(Arr::only($data, ['x0', 'x1', 'y0', 'y1']), $image, $isMyo);
            } else {
                $this->handleImage($data['thumbnail'], $image->imageDirectory, $image->thumbnailFileName, null, isset($data['default_image']));
            }

            // Process and save the image itself
            if (!$isMyo) {
                $this->processImage($image);

                // Auto-generate colours
                if (config('lorekeeper.character_pairing.auto_generate_colours')) {
                    $this->imageColours($image, Auth::user());
                }
            }

            // Attach features
            foreach ($data['feature_id'] as $key => $featureId) {
                if ($featureId) {
                    $feature = CharacterFeature::create(['character_image_id' => $image->id, 'feature_id' => $featureId, 'data' => $data['feature_data'][$key]]);
                }
            }

            return $image;
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return false;
    }

    /**
     * Generates a list of features for displaying.
     *
     * @param CharacterImage $image
     *
     * @return string
     */
    private function generateFeatureList($image) {
        $result = '';
        foreach ($image->features as $feature) {
            $result .= '<div>'.($feature->feature->category ? '<strong>'.$feature->feature->category->displayName.':</strong> ' : '').$feature->feature->displayName.'</div>';
        }

        return $result;
    }

    /**
     * Generates a list of image credits for displaying.
     *
     * @param CharacterImage $image
     *
     * @return string
     */
    private function generateCredits($image) {
        $result = ['designers' => '', 'artists' => ''];
        foreach ($image->designers as $designer) {
            $result['designers'] .= '<div>'.$designer->displayLink().'</div>';
        }
        foreach ($image->artists as $artist) {
            $result['artists'] .= '<div>'.$artist->displayLink().'</div>';
        }

        return $result;
    }

    /**
     * Handles character lineage data.
     *
     * @param array $data
     * @param mixed $character
     *
     * @return Character             $character
     * @return bool|CharacterLineage
     */
    private function handleCharacterLineage($data, $character) {
        try {
            if (!isset($data['parent_1_id']) && !isset($data['parent_1_name']) && !isset($data['parent_2_id']) && !isset($data['parent_2_name'])) {
                throw new \Exception('No lineage data provided.');
            }

            // check parent ids if set to see if character exists
            if (isset($data['parent_1_id']) && $data['parent_1_id']) {
                $parent_1 = Character::find($data['parent_1_id']);
                if (!$parent_1) {
                    throw new \Exception('Parent 1 is invalid.');
                }
            }
            if (isset($data['parent_2_id']) && $data['parent_2_id']) {
                $parent_2 = Character::find($data['parent_2_id']);
                if (!$parent_2) {
                    throw new \Exception('Parent 2 is invalid.');
                }
            }

            $lineage = CharacterLineage::create([
                'character_id'   => $character->id,
                'parent_1_id'    => $data['parent_1_id'] ?? null,
                'parent_1_name'  => $data['parent_1_id'] ? null : ($data['parent_1_name'] ?? null),
                'parent_2_id'    => $data['parent_2_id'] ?? null,
                'parent_2_name'  => $data['parent_2_id'] ? null : ($data['parent_2_name'] ?? null),
                'depth'          => $data['depth'] ?? 0,
            ]);

            return $this->commitReturn($lineage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return false;
    }
}
