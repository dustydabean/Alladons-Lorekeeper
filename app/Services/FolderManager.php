<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;

use App\Models\Character\CharacterFolder;

class FolderManager extends Service
{
    /**
     * Create folder
     */
    public function createFolder($data, $user)
    {
        DB::beginTransaction();

        try {

            if(!isset($data['name'])) throw new \Exception('Please provide a folder name.');

            $folder = CharacterFolder::create([
                'name' => $data['name'],
                'description' => isset($data['description']) ? $data['description'] : null,
                'user_id' => $user->id,
            ]);

            return $this->commitReturn($folder);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Create folder
     */
    public function editFolder($data, $user, $folder)
    {
        DB::beginTransaction();

        try {

            $folder->update([
                'name' => $data['name'],
                'description' => isset($data['description']) ? $data['description'] : null,
                'user_id' => $user->id,
            ]);

            return $this->commitReturn($folder);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * deletes a folder
     */
    public function deleteFolder($folder)
    {
        DB::beginTransaction();

        try {

            foreach($folder->characters as $character) {
                $character->folder_id = null;
                $character->save();
            }

            $folder->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}