<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\Character\Character;
use App\Models\Character\CharacterRelation;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CharacterLinkService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Character Link Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character relationship links.
    |
    */

    /**
     * @param mixed $character
     * @param mixed $slugs
     * @param mixed $user
     */
    public function createCharacterRelationLinks($character, $slugs, $user) {
        DB::beginTransaction();

        try {
            foreach ($slugs as $slug) {
                $otherCharacter = Character::where('slug', $slug)->first();
                if (!$otherCharacter) {
                    throw new \Exception('Character not found.');
                }

                if (!$character->is_links_open || !$otherCharacter->is_links_open) {
                    throw new \Exception("One or more character's links are closed to requests.");
                }

                // check if there is an existing link, the lower id is always character_1_id
                $lowerId = $character->id < $otherCharacter->id ? $character->id : $otherCharacter->id;
                $higherId = $character->id < $otherCharacter->id ? $otherCharacter->id : $character->id;
                if (CharacterRelation::where('character_1_id', $lowerId)->where('character_2_id', $higherId)->exists()) {
                    throw new \Exception('A relation already exists between one or more of these characters.');
                }

                if ($user->id == $otherCharacter->user_id) {
                    CharacterRelation::create([
                        'character_1_id' => $lowerId,
                        'character_2_id' => $higherId,
                        'status'         => 'Approved',
                    ]);
                } else {
                    $relation = CharacterRelation::create([
                        'character_1_id' => $lowerId,
                        'character_2_id' => $higherId,
                    ]);

                    // notify the other user
                    Notifications::create('LINK_REQUESTED', $otherCharacter->user, [
                        'character' => $character->fullname,
                        'requested' => $otherCharacter->fullname,
                        'link'      => $user->url,
                        'user'      => $user->name,
                        'id'        => $relation->id,
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
     * Accepts or rejects a link request.
     *
     * @param mixed $id
     * @param mixed $action
     */
    public function handleCharacterRelationLink($id, $action) {
        DB::beginTransaction();

        try {
            $link = CharacterRelation::find($id);
            if (!$link) {
                throw new \Exception('Link not found.');
            }

            if ($link->status != 'Pending') {
                throw new \Exception('Link is not pending.');
            }

            if ($action == 'accept') {
                $link->status = 'Approved';
                $link->save();

                $otherUserCharacter = $link->getOtherCharacter($link->getCharacterForUser(Auth::user()->id)->id);
                Notifications::create('LINK_ACCEPTED', $otherUserCharacter->user, [
                    'link'      => Auth::user()->url,
                    'user'      => Auth::user()->name,
                    'requested' => $link->getCharacterForUser(Auth::user()->id)->fullname,
                    'character' => $otherUserCharacter->url,
                ]);
            } else {
                $link->delete();
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a relationship link.
     *
     * @param mixed $id
     */
    public function deleteCharacterRelationLink($id) {
        DB::beginTransaction();

        try {
            $link = CharacterRelation::find($id);

            if (!$link) {
                throw new \Exception('Link not found.');
            }

            $link->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     *  this is when a user changes the relationship type.
     *
     * @param mixed $data
     * @param mixed $id
     * @param mixed $user
     */
    public function updateCharacterRelationLinkInfo($data, $id, $user) {
        DB::beginTransaction();

        try {
            $link = CharacterRelation::find($id);

            if (!$link) {
                throw new \Exception('Link not found.');
            }

            $character = Character::where('slug', $data['slug'])->first();
            if (!$character) {
                throw new \Exception('Character not found.');
            }

            if ($character->id == $link->character_1_id) {
                $link->info = [$data['info'], $link->info ? $link->info[1] : ''];
            } else {
                $link->info = [$link->info ? $link->info[0] : '', $data['info']];
            }

            if (isset($data['type'])) {
                $link->type = $data['type'];
            }

            $link->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
