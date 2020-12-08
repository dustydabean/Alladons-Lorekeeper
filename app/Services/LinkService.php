<?php namespace App\Services;

use App\Services\Service;

use DB;
use Auth;
use Config;
use Notifications;

use App\Models\Character\Character;
use App\Models\Character\CharacterRelation;
use App\Models\User\User;

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

    /**
     *   // This creates an abritrary link that serves to be made into a ''full'' link once the request is approved
     *   // If the character owners are the same (e.g one user owns both) the link will create and be fully developed
     *
     */
    public function createLink($chara1, $chara2, $owner = false) 
    {

    DB::beginTransaction();

    try {

        if(CharacterRelation::where('chara_1', $chara1)->where('chara_2', $chara2)->exists() || CharacterRelation::where('chara_1', $chara2)->where('chara_2', $chara1)->exists()) 
        {
            flash("A relation already exists between one or more of these characters.")->error();
            throw new \Exception;
        }

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

            $user = Auth::user();
            $character = Character::find($chara1);
            $link = Character::find($chara2);
            $requested = User::find($link->user_id);

            $relation = CharacterRelation::create([
                'chara_1' => $chara1,
                'chara_2' => $chara2,
            ]);

            Notifications::create('LINK_REQUESTED', $requested, [
                'character' => $character->fullname,
                'requested' => $link->fullname,
                'link' => $user->url,
                'user' => $user->name,
                'id' => $relation->id
            ]);
        }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function approveLink() 
    {
        // when a user approves a link
    }

    public function denyLink()
    {
        // when a user rejects a link
    }

    /**
     *   Deletes link
     *
     */
    public function deleteLink($data) 
    {
        DB::beginTransaction();

        try {

            $relation = CharacterRelation::where('chara_1', $data['chara_1'])->where('chara_2', $data['chara_2'])->first();
            $relation->inverse->delete();
            $relation->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     *  this is when a user changes the relationship type
     *
     */
    public function updateInfo($data) 
    {

        // types
        $types = [
            '???',
            'Acquaintence',
            'Best Friends',
            'Boss and Employee',
            'Co-workers',
            'Crushing',
            'Enemy',
            'Family',
            'Friends',
            'Frenemies',
            'It\'s Complicated',
            'Life Partners',
            'On-and-Off',
            'Partners in Crime',
            'Past Relationship',
            'Polyamorous Relationship',
            'Rival',
            'Roomate',
            'Significant Others',
        ];

        // info
            $info = $data['info'];
            $chara_1 = $data['chara_1'];
            $chara_2 = $data['chara_2'];
            $relation = CharacterRelation::where('chara_1', $chara_1)->where('chara_2', $chara_2)->first();

        // matching key types
        if(isset($data['type'])) {
                $key = $data['type'];
                $type = $types[$key];
        }
        else {
            $type = '???';
        }

            $relation->type = $type;
            $relation->info = $info;
            $relation->save();

    return redirect()->back();
    }
    
}