<?php

namespace App\Services;

use App\Models\User\User;
use App\Models\User\UserBlock;
use App\Models\User\UserFriend;
use Carbon\Carbon;
use DB;
use Notifications;

class InteractionService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Interaction Service
    |--------------------------------------------------------------------------
    |
    | Handles friend and block requests.
    |
    */

    /**
     * Sends a friend request to a user.
     */
    public function sendFriendRequest(User $initiator, User $recipient)
    {
        DB::beginTransaction();

        try {
            if ($recipient->isBlocked($initiator)) {
                throw new \Exception('You cannot send a friend request to a user who has blocked you.');
            }
            if ($initiator->isBlocked($recipient)) {
                throw new \Exception('You cannot send a friend request to a user who has blocked you.');
            }
            // check if the user is already friends with the other user
            if ($initiator->isPendingFriendsWith($recipient)) {
                throw new \Exception('You have a pending friend request with this user.');
            }
            if ($initiator->isFriendsWith($recipient)) {
                throw new \Exception('You are already friends with this user.');
            }

            // create a new UserFriend record
            $friend_request = UserFriend::create([
                'initiator_id' => $initiator->id,
                'recipient_id' => $recipient->id,
                'created_at'   => Carbon::now(),
            ]);

            // send a notification to the recipient
            Notifications::create('FRIEND_REQUEST_SENT', $recipient, [
                'sender_url' => $initiator->url,
                'sender'     => $initiator->name,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Accepts or rejects a friend request.
     *
     * @param mixed $request_id
     * @param mixed $accept
     */
    public function editFriendRequest($request_id, $accept)
    {
        DB::beginTransaction();

        try {

            // find pending request
            $friend_request = UserFriend::find($request_id);
            if (!$friend_request) {
                throw new \Exception('Friend request not found.');
            }

            if ($accept) {
                // set the friend request as accepted
                $friend_request->recipient_approved = 1;
                $friend_request->approved_at = Carbon::now();
                $friend_request->save();

                Notifications::create('FRIEND_REQUEST_ACCEPTED', $friend_request->initiator, [
                    'sender_url' => $friend_request->recipient->url,
                    'sender'     => $friend_request->recipient->name,
                ]);
            } else {
                // delete the friend request
                $friend_request->delete();
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * removes a friend.
     *
     * @param mixed $id
     */
    public function removeFriend($id)
    {
        DB::beginTransaction();

        try {

            // find pending request
            $friend = UserFriend::find($id);
            if (!$friend) {
                throw new \Exception('Friend not found.');
            }

            // delete the friend request
            $friend->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * block or unblock a user.
     *
     * @param mixed $user
     * @param mixed $blocked
     */
    public function blockUser($user, $blocked)
    {
        DB::beginTransaction();

        try {

            // check if they are already blocked
            if ($blocked->isBlocked($user)) {
                // delete userblock record
                $userblock = UserBlock::where('user_id', $user->id)->where('blocked_id', $blocked->id)->first();
                $userblock->delete();
            } else {
                // remove any friend requests / friendships
                $friend_request = UserFriend::
                    where('initiator_id', $user->id)->where('recipient_id', $blocked->id)
                    ->orWhere('initiator_id', $blocked->id)->where('recipient_id', $user->id)->first();
                if ($friend_request) {
                    $friend_request->delete();
                }
                // create a new UserBlock record
                $block = UserBlock::create([
                    'user_id'    => $user->id,
                    'blocked_id' => $blocked->id,
                ]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
