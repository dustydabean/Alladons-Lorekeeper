<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User\User;
use App\Models\User\UserFriend;
use App\Services\InteractionService;
use Auth;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * gets friends index.
     */
    public function getIndex()
    {
        return view('home.friend_index', [
            'friends' => Auth::user()->friends,
        ]);
    }

    /**
     * gets friend requests index.
     */
    public function getFriendRequests()
    {
        $sent_requests = UserFriend::where('recipient_approved', 0)->where('initiator_id', Auth::user()->id)->get();
        $received_requests = UserFriend::where('recipient_approved', 0)->where('recipient_id', Auth::user()->id)->get();

        return view('home.friend_request_index', [
            'sent_requests'     => $sent_requests,
            'received_requests' => $received_requests,
        ]);
    }

    /**
     * Initiates a friend request.
     *
     * @param mixed $id
     */
    public function sendFriendRequest(InteractionService $service, $id)
    {
        if ($service->sendFriendRequest(Auth::user(), User::find($id))) {
            flash('Request sent successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * accept or decline friend request.
     *
     * @param mixed $id
     * @param mixed $accept
     */
    public function postAcceptRequest(InteractionService $service, $id, $accept = 0)
    {
        if ($service->editFriendRequest($id, $accept)) {
            flash('Request '.($accept ? 'accepted' : 'rejected').' successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * removes a friend.
     *
     * @param mixed $id
     */
    public function postRemoveFriend(InteractionService $service, $id)
    {
        if ($service->removeFriend($id)) {
            flash('Friend removed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * block or unblock a user.
     *
     * @param mixed $id
     */
    public function postBlockUser(InteractionService $service, $id)
    {
        $check = User::find($id)->isBlocked(Auth::user());
        if ($service->blockUser(Auth::user(), User::find($id))) {
            flash('User '.($check ? 'un' : null).'blocked successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
