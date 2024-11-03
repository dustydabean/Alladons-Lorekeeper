<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Character\CharacterGeneration;
use App\Services\CharacterGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterGenerationController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Character Generation Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character generations.
    |
    */

    /**
     * Shows the character generation index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.characters.character_generations', [
            'generations' => CharacterGeneration::paginate(30),
        ]);
    }

    /**
     * Shows the create character generation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterGeneration() {
        return view('admin.characters.create_edit_character_generation', [
            'generation' => new CharacterGeneration,
        ]);
    }

    /**
     * Shows the edit character generation page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterGeneration($id) {
        $generation = CharacterGeneration::find($id);
        if (!$generation) {
            abort(404);
        }

        return view('admin.characters.create_edit_character_generation', [
            'generation' => $generation,
            'characters' => Character::where('generation_id', $generation->id)->get(),
        ]);
    }

    /**
     * Creates or edits a character generation.
     *
     * @param App\Services\CharacterGenerationService $service
     * @param int|null                              $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCharacterGeneration(Request $request, CharacterGenerationService $service, $id = null) {
        $id ? $request->validate(CharacterGeneration::$updateRules) : $request->validate(CharacterGeneration::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image',
        ]);
        if ($id && $service->updateCharacterGeneration(CharacterGeneration::find($id), $data, Auth::user())) {
            flash('Character generation updated successfully.')->success();
        } elseif (!$id && $generation = $service->createCharacterGeneration($data, Auth::user())) {
            flash('Character generation created successfully.')->success();

            return redirect()->to('admin/data/character-generations/edit/'.$generation->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the character generation deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCharacterGeneration($id) {
        $generation = CharacterGeneration::find($id);

        return view('admin.characters._delete_character_generation', [
            'generation' => $generation,
        ]);
    }

    /**
     * Deletes a character generation.
     *
     * @param App\Services\CharacterGenerationService $service
     * @param int                                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCharacterGeneration(Request $request, CharacterGenerationService $service, $id) {
        if ($id && $service->deleteCharacterGeneration(CharacterGeneration::find($id), Auth::user())) {
            flash('Character generation deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/character-generations');
    }
}
