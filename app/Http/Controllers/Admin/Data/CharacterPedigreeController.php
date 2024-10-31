<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Character\CharacterPedigree;
use App\Services\CharacterPedigreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterPedigreeController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Character Pedigree Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character pedigrees.
    |
    */

    /**
     * Shows the character pedigree index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.characters.character_pedigrees', [
            'pedigrees' => CharacterPedigree::paginate(30),
        ]);
    }

    /**
     * Shows the create character pedigree page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterPedigree() {
        return view('admin.characters.create_edit_character_pedigree', [
            'pedigree' => new CharacterPedigree,
        ]);
    }

    /**
     * Shows the edit character pedigree page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterPedigree($id) {
        $pedigree = CharacterPedigree::find($id);
        if (!$pedigree) {
            abort(404);
        }

        return view('admin.characters.create_edit_character_pedigree', [
            'pedigree' => $pedigree,
            'characters' => Character::where('pedigree_id', $pedigree->id)->get(),
        ]);
    }

    /**
     * Creates or edits a character pedigree.
     *
     * @param App\Services\CharacterPedigreeService $service
     * @param int|null                              $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCharacterPedigree(Request $request, CharacterPedigreeService $service, $id = null) {
        $id ? $request->validate(CharacterPedigree::$updateRules) : $request->validate(CharacterPedigree::$createRules);
        $data = $request->only([
            'name', 'description',
        ]);
        if ($id && $service->updateCharacterPedigree(CharacterPedigree::find($id), $data, Auth::user())) {
            flash('Character pedigree updated successfully.')->success();
        } elseif (!$id && $pedigree = $service->createCharacterPedigree($data, Auth::user())) {
            flash('Character pedigree created successfully.')->success();

            return redirect()->to('admin/data/character-pedigrees/edit/'.$pedigree->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the character pedigree deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCharacterPedigree($id) {
        $pedigree = CharacterPedigree::find($id);

        return view('admin.characters._delete_character_pedigree', [
            'pedigree' => $pedigree,
        ]);
    }

    /**
     * Deletes a character pedigree.
     *
     * @param App\Services\CharacterPedigreeService $service
     * @param int                                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCharacterPedigree(Request $request, CharacterPedigreeService $service, $id) {
        if ($id && $service->deleteCharacterPedigree(CharacterPedigree::find($id), Auth::user())) {
            flash('Character pedigree deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/character-pedigrees');
    }
}
