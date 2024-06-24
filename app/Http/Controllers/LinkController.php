<?php

namespace App\Http\Controllers;

use App\Services\CharacterLinkService;

class LinkController extends Controller {
    /**
     * Accepts or rejects a link request.
     *
     * @param mixed $action
     * @param mixed $id
     *
     * @return response 200
     */
    public function postHandleLink(CharacterLinkService $service, $action, $id) {
        if (!$service->handleCharacterRelationLink($id, $action, Auth::user())) {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return response()->json(['error' => 'Could not handle link request.'], 400);
        }

        flash('Link request '.$action.'ed successfully!', 'success');

        return response()->json(['success' => 'Link request '.$action.'ed successfully!'], 200);
    }
}
