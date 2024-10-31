<?php

namespace App\Services;

use App\Models\Character\Character;
use App\Models\Character\CharacterPedigree;
use Illuminate\Support\Facades\DB;

class CharacterPedigreeService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Character Pedigree Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character pedigrees.
    |
    */

    /**
     * Creates a new pedigree.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Character\CharacterPedigree|bool
     */
    public function createCharacterPedigree($data, $user) {
        DB::beginTransaction();

        try {
            if (CharacterPedigree::where('name', $data['name'])->exists()) {
                throw new \Exception('A pedigree with the given name already exists.');
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            }

            $pedigree = CharacterPedigree::create($data);

            if (!$this->logAdminAction($user, 'Created Pedigree', 'Created '.$pedigree->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($pedigree);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a pedigree.
     *
     * @param \App\Models\Character\CharacterPedigree $pedigree
     * @param array                         $data
     * @param \App\Models\User\User         $user
     *
     * @return \App\Models\Character\CharacterPedigree|bool
     */
    public function updateCharacterPedigree($pedigree, $data, $user) {
        DB::beginTransaction();

        try {
            if (CharacterPedigree::where('name', $data['name'])->where('id', '!=', $pedigree->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            }

            $pedigree->update($data);

            if (!$this->logAdminAction($user, 'Updated Pedigree', 'Updated '.$pedigree->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($pedigree);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a pedigree.
     *
     * @param \App\Models\Character\CharacterPedigree $pedigree
     * @param mixed                         $user
     *
     * @return bool
     */
    public function deleteCharacterPedigree($pedigree, $user) {
        DB::beginTransaction();

        try {
            // Check first if characters with this pedigree exist
            if (Character::where('pedigree_id', $pedigree->id)->exists()) {
                throw new \Exception('A character with this pedigree exists. Please change its pedigree first.');
            }

            if (!$this->logAdminAction($user, 'Deleted Pedigree', 'Deleted '.$pedigree->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            $pedigree->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
