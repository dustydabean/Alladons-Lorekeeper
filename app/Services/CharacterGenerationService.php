<?php

namespace App\Services;

use App\Models\Character\Character;
use App\Models\Character\CharacterGeneration;
use Illuminate\Support\Facades\DB;

class CharacterGenerationService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Character Generation Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character generations.
    |
    */

    /**
     * Creates a new generation.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Character\CharacterGeneration|bool
     */
    public function createCharacterGeneration($data, $user) {
        DB::beginTransaction();

        try {
            if (CharacterGeneration::where('name', $data['name'])->exists()) {
                throw new \Exception('A generation with the given name already exists.');
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            }

            $generation = CharacterGeneration::create($data);

            if (!$this->logAdminAction($user, 'Created Generation', 'Created '.$generation->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($generation);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a generation.
     *
     * @param \App\Models\Character\CharacterGeneration $generation
     * @param array                         $data
     * @param \App\Models\User\User         $user
     *
     * @return \App\Models\Character\CharacterGeneration|bool
     */
    public function updateCharacterGeneration($generation, $data, $user) {
        DB::beginTransaction();

        try {
            if (CharacterGeneration::where('name', $data['name'])->where('id', '!=', $generation->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            if (isset($data['description']) && $data['description']) {
                $data['parsed_description'] = parse($data['description']);
            }

            $generation->update($data);

            if (!$this->logAdminAction($user, 'Updated Generation', 'Updated '.$generation->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($generation);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a generation.
     *
     * @param \App\Models\Character\CharacterGeneration $generation
     * @param mixed                         $user
     *
     * @return bool
     */
    public function deleteCharacterGeneration($generation, $user) {
        DB::beginTransaction();

        try {
            // Check first if characters with this generation exist
            if (Character::where('generation_id', $generation->id)->exists()) {
                throw new \Exception('A character with this generation exists. Please change its generation first.');
            }

            if (!$this->logAdminAction($user, 'Deleted Generation', 'Deleted '.$generation->name)) {
                throw new \Exception('Failed to log admin action.');
            }

            $generation->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
