<?php

namespace App\Http\Controllers\Admin\Characters;

use Illuminate\Http\Request;

use Auth;
use Config;
use Settings;

use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Rarity;
use App\Models\User\User;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Feature\Feature;
use App\Models\Character\CharacterTransfer;
use App\Models\Trade;
use App\Models\User\UserItem;

use App\Services\CharacterManager;
use App\Services\CurrencyManager;
use App\Services\TradeManager;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterGenome;
use App\Models\Genetics\Loci;
use App\Models\Genetics\LociAllele;

class CharacterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Character Controller
    |--------------------------------------------------------------------------
    |
    | Handles admin creation/editing of characters and MYO slots.
    |
    */

    /**
     * Gets the next number for a character in a category.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @return string
     */
    public function getPullNumber(Request $request, CharacterManager $service)
    {
        return $service->pullNumber($request->get('category'));
    }

    /**
     * Shows the create character page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacter()
    {
        return view('admin.masterlist.create_character', [
            'categories' => CharacterCategory::orderBy('sort')->get(),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => ['0' => 'Pick a Species First'],
            'genes' => ['0' => 'Select Gene Group'] + Loci::orderBy('sort', 'desc')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray(),
            'isMyo' => false
        ]);
    }

    /**
     * Shows the create MYO slot page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateMyo()
    {
        return view('admin.masterlist.create_character', [
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => ['0' => 'Pick a Species First'],
            'genes' => ['0' => 'Select Gene Group'] + Loci::orderBy('sort', 'desc')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray(),
            'isMyo' => true
        ]);
    }

    /**
     * Shows the edit image subtype portion of the modal
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterMyoSubtype(Request $request) {
      $species = $request->input('species');
      return view('admin.masterlist._create_character_subtype', [
          'subtypes' => ['0' => 'Select Subtype'] + Subtype::where('species_id','=',$species)->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
          'isMyo' => $request->input('myo')
      ]);
    }

    /**
     * Gets the genes for character creation.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterMyoGenes(Request $request) {
        $loci = Loci::where('id', $request->input('loci'))->first();
        $alleles = $loci->getAlleles();
        return view('admin.masterlist._create_character_genetics', [
            'loci' => $loci,
            'alleles' => $alleles,
            'isMyo' => $request->input('myo'),
        ]);
    }

    /**
     * Creates a character.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateCharacter(Request $request, CharacterManager $service)
    {
        $request->validate(Character::$createRules);
        $data = $request->only([
            'user_id', 'owner_url', 'character_category_id', 'number', 'slug',
            'description', 'is_visible', 'is_giftable', 'is_tradeable', 'is_sellable',
            'sale_value', 'transferrable_at', 'use_cropper',
            'x0', 'x1', 'y0', 'y1',
            'designer_id', 'designer_url',
            'artist_id', 'artist_url',
            'species_id', 'subtype_id', 'rarity_id', 'feature_id', 'feature_data',
            'gene_id', 'gene_allele_id', 'gene_numeric_data', 'gene_gradient_data',
            'genome_visibility',
            'image', 'thumbnail', 'image_description'
        ]);
        if ($character = $service->createCharacter($data, Auth::user())) {
            flash('Character created successfully.')->success();
            return redirect()->to($character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Creates an MYO slot.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateMyo(Request $request, CharacterManager $service)
    {
        $request->validate(Character::$myoRules);
        $data = $request->only([
            'user_id', 'owner_url', 'name',
            'description', 'is_visible', 'is_giftable', 'is_tradeable', 'is_sellable',
            'sale_value', 'transferrable_at', 'use_cropper',
            'x0', 'x1', 'y0', 'y1',
            'designer_id', 'designer_url',
            'artist_id', 'artist_url',
            'species_id', 'subtype_id', 'rarity_id', 'feature_id', 'feature_data',
            'gene_id', 'gene_allele_id', 'gene_numeric_data', 'gene_gradient_data',
            'genome_visibility',
            'image', 'thumbnail'
        ]);
        if ($character = $service->createCharacter($data, Auth::user(), true)) {
            flash('MYO slot created successfully.')->success();
            return redirect()->to($character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Shows the edit character stats modal.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterStats($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin._edit_stats_modal', [
            'character' => $this->character,
            'categories' => CharacterCategory::orderBy('sort')->pluck('name', 'id')->toArray(),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'number' => format_masterlist_number($this->character->number, Config::get('lorekeeper.settings.character_number_digits')),
            'isMyo' => false
        ]);
    }

    /**
     * Shows the edit MYO stats modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMyoStats($id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);

        return view('character.admin._edit_stats_modal', [
            'character' => $this->character,
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'isMyo' => true
        ]);
    }

    /**
     * Edits a character's stats.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterStats(Request $request, CharacterManager $service, $slug)
    {
        $request->validate(Character::$updateRules);
        $data = $request->only([
            'character_category_id', 'number', 'slug',
            'is_giftable', 'is_tradeable', 'is_sellable', 'sale_value',
            'transferrable_at'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterStats($data, $this->character, Auth::user())) {
            flash('Character stats updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Edits an MYO slot's stats.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditMyoStats(Request $request, CharacterManager $service, $id)
    {
        $request->validate(Character::$myoRules);
        $data = $request->only([
            'name',
            'is_giftable', 'is_tradeable', 'is_sellable', 'sale_value',
            'transferrable_at'
        ]);
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterStats($data, $this->character, Auth::user())) {
            flash('Character stats updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Shows the create character genome modal.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterGenome($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        return $this->getCreateGenome(false);
    }

    /**
     * Shows the edit character genome modal.
     *
     * @param  string  $slug
     * @param  int     $gid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterGenome($slug, $gid)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        return $this->getEditGenome($gid, false);
    }

    /**
     * Shows the create myo genome modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateMyoGenome($id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        return $this->getCreateGenome(true);
    }

    /**
     * Shows the edit myo genome modal.
     *
     * @param  int  $id
     * @param  int  $gid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMyoGenome($id, $gid)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        return $this->getEditGenome($gid, true);
    }

    /**
     * Shows the create genome modal.
     *
     * @param  int  $isMyo
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function getCreateGenome($isMyo) {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        if(!$this->character) abort(404);
        return view('character.admin._edit_genes_modal', [
            'character' => $this->character,
            'genome' => new CharacterGenome,
            'genes' => Loci::orderBy('sort')->pluck('name', 'id')->toArray(),
            'isMyo' => $isMyo,
        ]);
    }

    /**
     * Shows the edit genome modal.
     *
     * @param  int  $id
     * @param  int  $isMyo
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function getEditGenome($id, $isMyo) {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        if(!$this->character) abort(404);
        $this->genome = $this->character->genomes->where('id', $id)->first();
        if(!$this->genome) abort(404);
        return view('character.admin._edit_genes_modal', [
            'character' => $this->character,
            'genome' => $this->genome,
            'genes' => Loci::orderBy('sort')->pluck('name', 'id')->toArray(),
            'isMyo' => $isMyo,
        ]);
    }

    /**
     * Edits a character's genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @param  int                            $gid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterGenome(Request $request, CharacterManager $service, $slug, $gid)
    {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        $data = $request->only([
            'gene_id', 'gene_allele_id', 'gene_gradient_data', 'gene_numeric_data',
            'genome_visibility',
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        $this->genome = CharacterGenome::where('character_id', $this->character->id)->where('id', $gid)->first();
        if(!$this->genome) abort(404);
        if ($service->updateCharacterGenome($data, $this->character, $this->genome, Auth::user())) {
            flash('Character genome updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Edits a myo's genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @param  int                            $gid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditMyoGenome(Request $request, CharacterManager $service, $id, $gid)
    {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        $data = $request->only([
            'gene_id', 'gene_allele_id', 'gene_gradient_data', 'gene_numeric_data',
            'genome_visibility',
        ]);
        $this->character = Character::where('id', $id)->first();
        if(!$this->character || !$this->character->is_myo_slot) abort(404);
        $this->genome = CharacterGenome::where('character_id', $id)->where('id', $gid)->first();
        if(!$this->genome) abort(404);
        if ($service->updateCharacterGenome($data, $this->character, $this->genome, Auth::user())) {
            flash('Character genome updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Creates a character genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateCharacterGenome(Request $request, CharacterManager $service, $slug)
    {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        $data = $request->only([
            'gene_id', 'gene_allele_id', 'gene_gradient_data', 'gene_numeric_data',
            'genome_visibility',
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterGenome($data, $this->character, null, Auth::user())) {
            flash('Character genome created successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Creates a myo's genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateMyoGenome(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        $data = $request->only([
            'gene_id', 'gene_allele_id', 'gene_gradient_data', 'gene_numeric_data',
            'genome_visibility',
        ]);
        $this->character = Character::where('id', $id)->first();
        if(!$this->character || !$this->character->is_myo_slot) abort(404);
        if ($service->updateCharacterGenome($data, $this->character, null, Auth::user())) {
            flash('Character genome created successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Shows the delete character genome modal.
     *
     * @param  string  $slug
     * @param  int     $gid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCharacterGenome($slug, $id)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        return $this->getDeleteGenome($id, false);
    }

    /**
     * Shows the delete myo genome modal.
     *
     * @param  int  $id
     * @param  int  $gid
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteMyoGenome($id, $gid)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        return $this->getDeleteGenome($gid, true);
    }

    /**
     * Shows the delete character/myo genome modal.
     *
     * @param  int   $id
     * @param  bool  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    private function getDeleteGenome($id, $isMyo) {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        if(!$this->character) abort(404);
        $this->genome = $this->character->genomes->where('id', $id)->first();
        if(!$this->genome) abort(404);
        return view('character.admin._delete_genome_modal', [
            'character' => $this->character,
            'genome' => $this->genome,
            'isMyo' => $isMyo,
        ]);
    }

    /**
     * Deletes a character's genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCharacterGenome(Request $request, CharacterManager $service, $slug, $id)
    {
        $this->character = Character::where('slug', $slug)->first();
        return $this->postDeleteGenome($request, $service, $id);
    }

    /**
     * Deletes a myo's genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @param  int                            $gid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteMyoGenome(Request $request, CharacterManager $service, $id, $gid)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        return $this->postDeleteGenome($request, $service, $gid);
    }

    /**
     * Deletes a genome.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postDeleteGenome(Request $request, CharacterManager $service, $id) {
        if(!Auth::user()->hasPower("view_hidden_genetics")) abort(404);
        if(!$this->character) abort(404);
        $this->genome = $this->character->genomes->where('id', $id)->first();
        if(!$this->genome) abort(404);
        if ($service->deleteCharacterGenome($this->character, $this->genome, Auth::user())) {
            flash('Character genome deleted successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Shows the edit character description modal.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterDescription($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin._edit_description_modal', [
            'character' => $this->character,
            'isMyo' => false
        ]);
    }

    /**
     * Shows the edit MYO slot description modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMyoDescription($id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);

        return view('character.admin._edit_description_modal', [
            'character' => $this->character,
            'isMyo' => true
        ]);
    }

    /**
     * Edits a character's description.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterDescription(Request $request, CharacterManager $service, $slug)
    {
        $data = $request->only([
            'description'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterDescription($data, $this->character, Auth::user())) {
            flash('Character description updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Edits an MYO slot's description.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditMyoDescription(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only([
            'description'
        ]);
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterDescription($data, $this->character, Auth::user())) {
            flash('Character description updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Edits a character's settings.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterSettings(Request $request, CharacterManager $service, $slug)
    {
        $data = $request->only([
            'is_visible'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterSettings($data, $this->character, Auth::user())) {
            flash('Character settings updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Edits an MYO slot's settings.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMyoSettings(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only([
            'is_visible'
        ]);
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterSettings($data, $this->character, Auth::user())) {
            flash('Character settings updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }

    /**
     * Shows the delete character modal.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterDelete($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin._delete_character_modal', [
            'character' => $this->character,
            'isMyo' => false
        ]);
    }

    /**
     * Shows the delete MYO slot modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyoDelete($id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);

        return view('character.admin._delete_character_modal', [
            'character' => $this->character,
            'isMyo' => true
        ]);
    }

    /**
     * Deletes a character.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterDelete(Request $request, CharacterManager $service, $slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        if ($service->deleteCharacter($this->character, Auth::user())) {
            flash('Character deleted successfully.')->success();
            return redirect()->to('masterlist');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Deletes an MYO slot.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMyoDelete(Request $request, CharacterManager $service, $id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);

        if ($service->deleteCharacter($this->character, Auth::user())) {
            flash('Character deleted successfully.')->success();
            return redirect()->to('myos');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Transfers a character.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTransfer(Request $request, CharacterManager $service, $slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        if($service->adminTransfer($request->only(['recipient_id', 'recipient_url', 'cooldown', 'reason']), $this->character, Auth::user())) {
            flash('Character transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Transfers an MYO slot.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMyoTransfer(Request $request, CharacterManager $service, $id)
    {
        $this->character = Character::where('is_myo_slot', 1)->where('id', $id)->first();
        if(!$this->character) abort(404);

        if($service->adminTransfer($request->only(['recipient_id', 'recipient_url', 'cooldown', 'reason']), $this->character, Auth::user())) {
            flash('Character transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the character transfer queue.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransferQueue($type)
    {
        $transfers = CharacterTransfer::query();
        $user = Auth::user();

        if($type == 'completed') $transfers->completed();
        else if ($type == 'incoming') $transfers->active()->where('is_approved', 0);
        else abort(404);

        $openTransfersQueue = Settings::get('open_transfers_queue');

        return view('admin.masterlist.character_transfers', [
            'transfers' => $transfers->orderBy('id', 'DESC')->paginate(20),
            'transfersQueue' => Settings::get('open_transfers_queue'),
            'openTransfersQueue' => $openTransfersQueue,
            'transferCount' => $openTransfersQueue ? CharacterTransfer::active()->where('is_approved', 0)->count() : 0,
            'tradeCount' => $openTransfersQueue ? Trade::where('status', 'Pending')->count() : 0
        ]);
    }

    /**
     * Shows the character transfer action modal.
     *
     * @param  int     $id
     * @param  string  $action
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransferModal($id, $action)
    {
        if($action != 'approve' && $action != 'reject') abort(404);
        $transfer = CharacterTransfer::where('id', $id)->active()->first();
        if(!$transfer) abort(404);

        return view('admin.masterlist._'.$action.'_modal', [
            'transfer' => $transfer,
            'cooldown' => Settings::get('transfer_cooldown'),
        ]);
    }

    /**
     * Acts on a transfer in the transfer queue.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTransferQueue(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::check()) abort(404);

        $action = $request->get('action');

        if($service->processTransferQueue($request->only(['action', 'cooldown', 'reason']) + ['transfer_id' => $id], Auth::user())) {
            if (strtolower($action) == 'approve') {
                flash('Transfer ' . strtolower($action) . 'd.')->success();
            } else {
                flash('Transfer ' . strtolower($action) . 'ed.')->success();
            }
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the character trade queue.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTradeQueue($type)
    {
        $trades = Trade::query();
        $user = Auth::user();

        if($type == 'completed') $trades->completed();
        else if ($type == 'incoming') $trades->where('status', 'Pending');
        else abort(404);

        $openTransfersQueue = Settings::get('open_transfers_queue');

        $stacks = array();
        foreach($trades->get() as $trade) {
            foreach($trade->data as $side=>$assets) {
                if(isset($assets['user_items'])) {
                    $user_items = UserItem::with('item')->find(array_keys($assets['user_items']));
                    $items = array();
                    foreach($assets['user_items'] as $id=>$quantity) {
                        $user_item = $user_items->find($id);
                        $user_item['quantity'] = $quantity;
                        array_push($items,$user_item);
                    }
                    $items = collect($items)->groupBy('item_id');
                    $stacks[$trade->id][$side] = $items;
                }
            }
        }

        return view('admin.masterlist.character_trades', [
            'trades' => $trades->orderBy('id', 'DESC')->paginate(20),
            'tradesQueue' => Settings::get('open_transfers_queue'),
            'openTransfersQueue' => $openTransfersQueue,
            'transferCount' => $openTransfersQueue ? CharacterTransfer::active()->where('is_approved', 0)->count() : 0,
            'tradeCount' => $openTransfersQueue ? Trade::where('status', 'Pending')->count() : 0,
            'stacks' => $stacks
        ]);
    }

    /**
     * Shows the character trade action modal.
     *
     * @param  int     $id
     * @param  string  $action
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTradeModal($id, $action)
    {
        if($action != 'approve' && $action != 'reject') abort(404);
        $trade = Trade::where('id', $id)->first();
        if(!$trade) abort(404);

        return view('admin.masterlist._'.$action.'_trade_modal', [
            'trade' => $trade,
            'cooldown' => Settings::get('transfer_cooldown'),
        ]);
    }

    /**
     * Acts on a trade in the trade queue.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTradeQueue(Request $request, TradeManager $service, $id)
    {
        if(!Auth::check()) abort(404);

        $action = strtolower($request->get('action'));
        if($action == 'approve' && $service->approveTrade($request->only(['action', 'cooldowns']) + ['id' => $id], Auth::user())) {
            flash('Trade approved.')->success();
        }
        else if($action == 'reject' && $service->rejectTrade($request->only(['action', 'reason']) + ['id' => $id], Auth::user())) {
            flash('Trade rejected.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**
     * Shows a list of all existing MYO slots.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyoIndex()
    {
        return view('admin.masterlist.myo_index', [
            'slots' => Character::myo(1)->orderBy('id', 'DESC')->paginate(30),
        ]);
    }
}
