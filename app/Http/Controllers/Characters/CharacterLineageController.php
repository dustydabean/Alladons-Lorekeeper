<?php

namespace App\Http\Controllers\Characters;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;

class CharacterLineageController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Character Lineage Controller
    |--------------------------------------------------------------------------
    |
    | Handles display of character lineage pages.
    |
    */

    /**
     * Shows the character lineage page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLineage($slug) {
        return $this->getLineagePage($slug, false);
    }

    /**
     * Shows the MYO slot lineage page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyoLineage($id) {
        return $this->getLineagePage($id, true);
    }

    /**
     * Shows the character's lineage page.
     *
     * @param mixed $id
     * @param mixed $isMyo
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLineagePage($id, $isMyo = false) {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if (!$this->character) {
            abort(404);
        }
        // if they cannot have a lineage abort 404
        if ($this->character->getLineageBlacklistLevel() > 1) {
            abort(404);
        }

        return view('character.lineage', [
            'character' => $this->character,
            'lineage'   => $this->character->lineage,
            'isMyo'     => $isMyo,
        ]);
    }
}
