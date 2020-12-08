<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Notifications;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterRelation;

use App\Services\LinkService;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\AccountController;

class LinkController extends Controller
{
    public function getAcceptLink($id)
    {
        $this->deleteNotif();

        if(!$this->postAcceptLink($id)) {
            flash('Link accepted succesfully!')->success();
            return redirect()->back();
        }
        else {
            flash('Something went wrong :(')->error();
            return redirect()->back();
        }
    }

    public function getRejectLink($id)
    {
        $this->deleteNotif();

        $this->postRejectLink($id);
    }

    public function postAcceptLink($id)
    {
        $user = Auth::user();

        $relation = CharacterRelation::find($id);
        if(!$relation)
        {
            flash("You have already rejected this relation, or it was deleted.")->error();
            return redirect()->back();
        }

        $chara1 = $relation->chara_1;
        $chara2 = $relation->chara_2;

        $link = Character::find($chara2);
        $character = Character::find($chara1);

        $sender =  User::find($character->user_id);

        if(CharacterRelation::where('chara_1', $chara2)->where('chara_2', $chara1)->exists())
        {
            flash("You have already accepted this relation!")->error();
            return redirect()->back();
        }

            CharacterRelation::create([
                'chara_1' => $chara2,
                'chara_2' => $chara1,
                'status' => 'Approved'
            ]);

        $relation->status = 'Approved';
        $relation->save();

            Notifications::create('LINK_ACCEPTED', $sender, [
                'requested' => $character->fullname,
                'link' => $user->url,
                'user' => $user->name,
                'character' => $character->url
            ]);
    }

    public function postRejectLink($id)
    {
        $relation = CharacterRelation::find($id);
        if($relation->inverse->exists())
        {
            $relation->inverse->delete();
        }
        $relation->delete();

        flash('Link rejected successfully.')->success();
        return redirect()->to('/notifications');
    }

    private function deleteNotif()
    {
        $notification = session('notification');

        $account = new AccountController;

        if($account->getDeleteNotification($notification))
        {

        }
        else {
            flash('Something went wrong :(')->error();
            return redirect()->back();
        }
    }

}