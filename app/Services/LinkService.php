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
    public function createLink($chara1, $chara2, $owner = false) {

    DB::beginTransaction();

    try {

        if($owner == True) {
            CharacterRelation::create([
                'chara_1' => $chara1,
                'chara_2' => $chara2,
                'status' => 'Approved'
            ]);

            CharacterRelation::create([
                'chara_1' => $chara2,
                'chara_2' => $chara1,
                'status' => 'Approved'
            ]);
        }
        else {
            CharacterRelation::create([
                'chara_1' => $chara1,
                'chara_2' => $chara2,
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