<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Character\Character;
use App\Models\Character\CharacterRelation;


class LinkService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Link Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character links.
    |
    */

    // This creates an abritrary link that serves to be made into a ''full'' link once the request is approved
    // If the character owners are the same (e.g one user owns both) the link will create and be fully developed
    public function createLink($data, $owner = false) {

    DB::beginTransaction();

    try {

        if($owner == True) {
            CharacterRelation::create([
                'data' => json_encode(['ids' => $data]),
                'status' => 'Approved'
            ]);
        }
        else {
            CharacterRelation::create([
                'data' => json_encode(['ids' => $data]),
            ]);
        }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function addLink() {
        
    }
    
}