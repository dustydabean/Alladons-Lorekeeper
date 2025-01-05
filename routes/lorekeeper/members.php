<?php

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
|
| Routes for logged in users with a linked dA account.
|
*/

/**************************************************************************************************
    Users
**************************************************************************************************/

Route::group(['prefix' => 'notifications', 'namespace' => 'Users'], function () {
    Route::get('/', 'AccountController@getNotifications');
    Route::get('delete/{id}', 'AccountController@getDeleteNotification');
    Route::post('clear', 'AccountController@postClearNotifications');
    Route::post('clear/{type}', 'AccountController@postClearNotifications');
});

Route::group(['prefix' => 'account', 'namespace' => 'Users'], function () {
    Route::get('settings', 'AccountController@getSettings');
    Route::post('profile', 'AccountController@postProfile');
    Route::post('theme', 'AccountController@postTheme');
    Route::post('staff-profile', 'AccountController@postStaffProfile');
    Route::post('staff-links', 'AccountController@postStaffLinks');
    Route::post('password', 'AccountController@postPassword');
    Route::post('email', 'AccountController@postEmail');
    Route::post('avatar', 'AccountController@postAvatar');
    Route::post('theme', 'AccountController@postTheme');
    Route::get('aliases', 'AccountController@getAliases');
    Route::get('make-primary/{id}', 'AccountController@getMakePrimary');
    Route::post('make-primary/{id}', 'AccountController@postMakePrimary');
    Route::get('hide-alias/{id}', 'AccountController@getHideAlias');
    Route::post('hide-alias/{id}', 'AccountController@postHideAlias');
    Route::get('remove-alias/{id}', 'AccountController@getRemoveAlias');
    Route::post('remove-alias/{id}', 'AccountController@postRemoveAlias');
    Route::post('dob', 'AccountController@postBirthday');
    Route::post('devlog-notif', 'AccountController@postdevLogNotif');
    Route::post('warning', 'AccountController@postWarningVisibility');

    Route::get('two-factor/confirm', 'AccountController@getConfirmTwoFactor');
    Route::post('two-factor/enable', 'AccountController@postEnableTwoFactor');
    Route::post('two-factor/confirm', 'AccountController@postConfirmTwoFactor');
    Route::post('two-factor/disable', 'AccountController@postDisableTwoFactor');

    Route::get('deactivate', 'AccountController@getDeactivate');
    Route::get('deactivate-confirm', 'AccountController@getDeactivateConfirmation');
    Route::post('deactivate', 'AccountController@postDeactivate');

    Route::get('bookmarks', 'BookmarkController@getBookmarks');
    Route::get('bookmarks/create', 'BookmarkController@getCreateBookmark');
    Route::get('bookmarks/edit/{id}', 'BookmarkController@getEditBookmark');
    Route::post('bookmarks/create', 'BookmarkController@postCreateEditBookmark');
    Route::post('bookmarks/edit/{id}', 'BookmarkController@postCreateEditBookmark');
    Route::get('bookmarks/delete/{id}', 'BookmarkController@getDeleteBookmark');
    Route::post('bookmarks/delete/{id}', 'BookmarkController@postDeleteBookmark');
});

Route::group(['prefix' => 'inventory', 'namespace' => 'Users'], function () {
    Route::get('/', 'InventoryController@getIndex');
    Route::post('edit', 'InventoryController@postEdit');
    Route::get('account-search', 'InventoryController@getAccountSearch');
    Route::get('full-inventory', 'InventoryController@getFullInventory');
    Route::get('consolidate-inventory', 'InventoryController@getConsolidateInventory');
    Route::post('consolidate', 'InventoryController@postConsolidateInventory');

    Route::get('selector', 'InventoryController@getSelector');
});

Route::group(['prefix' => 'pets', 'namespace' => 'Users'], function () {
    Route::get('/', 'PetController@getIndex');
    Route::post('transfer/{id}', 'PetController@postTransfer');
    Route::post('delete/{id}', 'PetController@postDelete');
    Route::post('name/{id}', 'PetController@postName');
    Route::post('attach/{id}', 'PetController@postAttach');
    Route::post('detach/{id}', 'PetController@postDetach');
    Route::post('variant/{id}', 'PetController@postVariant');
    Route::post('evolution/{id}', 'PetController@postEvolution');

    Route::get('selector', 'PetController@getSelector');
    Route::post('collect/{id}', 'PetController@postClaimPetDrops');
    Route::post('collect-all', 'PetController@postClaimAllPetDrops');
    Route::post('image/{id}', 'PetController@postCustomImage');
    Route::post('description/{id}', 'PetController@postDescription');

    Route::get('view/{id}', 'PetController@getPetPage')->where('id', '[0-9]+');
    Route::post('view/{id}/edit', 'PetController@postEditPetProfile')->where('id', '[0-9]+');

    Route::post('bond/{id}', 'PetController@postBond');
});

Route::group(['prefix' => 'characters', 'namespace' => 'Users'], function () {
    Route::get('/', 'CharacterController@getIndex');
    Route::post('sort', 'CharacterController@postSortCharacters');

    Route::post('{slug}/pets/sort', 'CharacterController@postSortCharacterPets');

    Route::get('transfers/{type}', 'CharacterController@getTransfers');
    Route::post('transfer/act/{id}', 'CharacterController@postHandleTransfer');

    Route::get('myos', 'CharacterController@getMyos');

    Route::get('pairings', 'PairingController@getPairings')->where('type', 'new|pending|approval|closed');
    Route::get('pairings/check', 'PairingController@checkPairings');
    Route::post('pairings/create', 'PairingController@createPairings');
    Route::post('pairings/cancel/{id}', 'PairingController@cancelPairing');
    Route::post('pairings/approve/{id}', 'PairingController@approvePairing');
    Route::post('pairings/reject/{id}', 'PairingController@rejectPairing');
    Route::post('pairings/complete/{id}', 'PairingController@createMyos');
});

Route::group(['prefix' => 'bank', 'namespace' => 'Users'], function () {
    Route::get('/', 'BankController@getIndex');
    Route::post('transfer', 'BankController@postTransfer');
    Route::get('convert/{id}', 'BankController@getConvertCurrency');
    Route::get('convert/{currency_id}/rate/{conversion_id}', 'BankController@getConvertCurrencyRate');
    Route::post('convert', 'BankController@postConvertCurrency');
});

Route::group(['prefix' => 'trades', 'namespace' => 'Users'], function () {
    Route::get('{status}', 'TradeController@getIndex')->where('status', 'open|pending|completed|rejected|canceled');
    Route::get('create', 'TradeController@getCreateTrade');
    Route::get('{id}/edit', 'TradeController@getEditTrade')->where('id', '[0-9]+');
    Route::post('create', 'TradeController@postCreateTrade');
    Route::post('{id}/edit', 'TradeController@postEditTrade')->where('id', '[0-9]+');
    Route::get('{id}', 'TradeController@getTrade')->where('id', '[0-9]+');

    Route::get('{id}/confirm-offer', 'TradeController@getConfirmOffer');
    Route::post('{id}/confirm-offer', 'TradeController@postConfirmOffer');
    Route::get('{id}/confirm-trade', 'TradeController@getConfirmTrade');
    Route::post('{id}/confirm-trade', 'TradeController@postConfirmTrade');
    Route::get('{id}/cancel-trade', 'TradeController@getCancelTrade');
    Route::post('{id}/cancel-trade', 'TradeController@postCancelTrade');
});

Route::group(['prefix' => 'crafting', 'namespace' => 'Users'], function() {
    Route::get('/', 'CraftingController@getIndex');
    Route::get('craft/{id}', 'CraftingController@getCraftRecipe');
    Route::post('craft/{id}', 'CraftingController@postCraftRecipe');
});

/**************************************************************************************************
    Characters
**************************************************************************************************/
Route::group(['prefix' => 'character', 'namespace' => 'Characters'], function () {
    Route::get('{slug}/profile/edit', 'CharacterController@getEditCharacterProfile');
    Route::post('{slug}/profile/edit', 'CharacterController@postEditCharacterProfile');

    Route::post('{slug}/inventory/edit', 'CharacterController@postInventoryEdit');

    Route::post('{slug}/bank/transfer', 'CharacterController@postCurrencyTransfer');
    Route::get('{slug}/transfer', 'CharacterController@getTransfer');
    Route::post('{slug}/transfer', 'CharacterController@postTransfer');
    Route::post('{slug}/transfer/{id}/cancel', 'CharacterController@postCancelTransfer');

    Route::post('{slug}/approval', 'CharacterController@postCharacterApproval');
    Route::get('{slug}/approval', 'CharacterController@getCharacterApproval');

    Route::get('{slug}/image/colours', function ($slug) {
        if (!config('lorekeeper.character_pairing.colours')) {
            return '';
        }

        return App\Models\Character\Character::where('slug', $slug)->first()?->image->displayColours();
    });
    // LINKS
    Route::get('{slug}/links/edit', 'CharacterController@getCreateEditCharacterLinks');
    Route::post('{slug}/links/edit', 'CharacterController@postCreateEditCharacterLinks');
    Route::post('{slug}/links/info/{id}', 'CharacterController@postEditCharacterLinkInfo');
    Route::get('{slug}/links/delete/{id}', 'CharacterController@getDeleteCharacterLink');
    Route::post('{slug}/links/delete/{id}', 'CharacterController@postDeleteCharacterLink');
});

// CHARACTER RELATIONSHIPS
Route::post('links/{action}/{id}', 'LinkController@postHandleLink')->where('action', 'accept|reject');

Route::group(['prefix' => 'myo', 'namespace' => 'Characters'], function () {
    Route::get('{id}/profile/edit', 'MyoController@getEditCharacterProfile');
    Route::post('{id}/profile/edit', 'MyoController@postEditCharacterProfile');

    Route::get('{id}/transfer', 'MyoController@getTransfer');
    Route::post('{id}/transfer', 'MyoController@postTransfer');
    Route::post('{id}/transfer/{id2}/cancel', 'MyoController@postCancelTransfer');

    Route::post('{id}/approval', 'MyoController@postCharacterApproval');
    Route::get('{id}/approval', 'MyoController@getCharacterApproval');
});

/**************************************************************************************************
    Submissions
**************************************************************************************************/

Route::group(['prefix' => 'gallery'], function () {
    Route::get('submissions/{type}', 'GalleryController@getUserSubmissions')->where('type', 'draft|pending|accepted|rejected');

    Route::post('favorite/{id}', 'GalleryController@postFavoriteSubmission');

    Route::get('submit/{id}', 'GalleryController@getNewGallerySubmission');
    Route::get('submit/character/{slug}', 'GalleryController@getCharacterInfo');
    Route::get('edit/{id}', 'GalleryController@getEditGallerySubmission');
    Route::get('queue/{id}', 'GalleryController@getSubmissionLog');
    Route::post('queue/totals/{id}', 'GalleryController@postSubmissionTotals');
    Route::post('submit', 'GalleryController@postCreateEditGallerySubmission');
    Route::post('edit/{id}', 'GalleryController@postCreateEditGallerySubmission');

    Route::post('collaborator/{id}', 'GalleryController@postEditCollaborator');

    Route::get('archive/{id}', 'GalleryController@getArchiveSubmission');
    Route::post('archive/{id}', 'GalleryController@postArchiveSubmission');
});

Route::group(['prefix' => 'submissions', 'namespace' => 'Users'], function () {
    Route::get('/', 'SubmissionController@getIndex');
    Route::get('new', 'SubmissionController@getNewSubmission');
    Route::get('new/character/{slug}', 'SubmissionController@getCharacterInfo');
    Route::get('new/character-permissions/{slug}', 'SubmissionController@getCharacterPermissions');
    Route::get('new/prompt/{id}', 'SubmissionController@getPromptInfo');
    Route::post('new', 'SubmissionController@postNewSubmission');
    Route::post('new/{draft}', 'SubmissionController@postNewSubmission')->where('draft', 'draft');
    Route::get('draft/{id}', 'SubmissionController@getEditSubmission');
    Route::post('draft/{id}', 'SubmissionController@postEditSubmission');
    Route::post('draft/{id}/{submit}', 'SubmissionController@postEditSubmission')->where('submit', 'submit');
    Route::post('draft/{id}/delete', 'SubmissionController@postDeleteSubmission');
    Route::post('draft/{id}/cancel', 'SubmissionController@postCancelSubmission');
});

Route::group(['prefix' => 'claims', 'namespace' => 'Users'], function () {
    Route::get('/', 'SubmissionController@getClaimsIndex');
    Route::get('new', 'SubmissionController@getNewClaim');
    Route::post('new', 'SubmissionController@postNewClaim');
    Route::post('new/{draft}', 'SubmissionController@postNewClaim')->where('draft', 'draft');
    Route::get('draft/{id}', 'SubmissionController@getEditClaim');
    Route::post('draft/{id}', 'SubmissionController@postEditClaim');
    Route::post('draft/{id}/{submit}', 'SubmissionController@postEditClaim')->where('submit', 'submit');
    Route::post('draft/{id}/delete', 'SubmissionController@postDeleteClaim');
    Route::post('draft/{id}/cancel', 'SubmissionController@postCancelClaim');
});

Route::group(['prefix' => 'reports', 'namespace' => 'Users'], function () {
    Route::get('/', 'ReportController@getReportsIndex');
    Route::get('new', 'ReportController@getNewReport');
    Route::post('new', 'ReportController@postNewReport');
    Route::get('view/{id}', 'ReportController@getReport');
});

Route::group(['prefix' => 'designs', 'namespace' => 'Characters'], function () {
    Route::get('{type?}', 'DesignController@getDesignUpdateIndex')->where('type', 'draft|pending|approved|rejected');
    Route::get('{id}', 'DesignController@getDesignUpdate');

    Route::get('{id}/comments', 'DesignController@getComments');
    Route::post('{id}/comments', 'DesignController@postComments');

    Route::get('{id}/image', 'DesignController@getImage');
    Route::post('{id}/image', 'DesignController@postImage');

    Route::get('{id}/addons', 'DesignController@getAddons');
    Route::post('{id}/addons', 'DesignController@postAddons');

    Route::get('{id}/traits', 'DesignController@getFeatures');
    Route::post('{id}/traits', 'DesignController@postFeatures');
    Route::get('traits/subtype', 'DesignController@getFeaturesSubtype');

    Route::get('{id}/confirm', 'DesignController@getConfirm');
    Route::post('{id}/submit', 'DesignController@postSubmit');

    Route::get('{id}/delete', 'DesignController@getDelete');
    Route::post('{id}/delete', 'DesignController@postDelete');

    Route::get('{id}/cancel', 'DesignController@getCancel');
    Route::post('{id}/cancel', 'DesignController@postCancel');
});

/**************************************************************************************************
    Shops
**************************************************************************************************/

Route::group(['prefix' => 'shops'], function () {
    Route::post('buy', 'ShopController@postBuy');
    Route::get('history', 'ShopController@getPurchaseHistory');
});

/**************************************************************************************************
    Dailies
**************************************************************************************************/

Route::group(['prefix' => __('dailies.dailies')], function () {
    // throttle requests to 1 per ~10 seconds
    Route::middleware('throttle:1,0.16')->group(function () {
        Route::post('{id}', 'DailyController@postRoll');
    });
});

/**************************************************************************************************
    Activities
 **************************************************************************************************/

Route::group(['prefix' => 'activities'], function () {
    Route::get('/', 'ActivityController@getIndex');
    Route::get('{id}', 'ActivityController@getActivity')->where(['id' => '[0-9]+']);
    Route::post('{id}/act', 'ActivityController@postAct')->where(['id' => '[0-9]+']);
});

/**************************************************************************************************
    Scavenger Hunts
**************************************************************************************************/

Route::group(['prefix' => 'hunts'], function() {
    Route::get('{id}', 'HuntController@getHunt');
    Route::get('targets/{pageId}', 'HuntController@getTarget');
    Route::post('targets/claim', 'HuntController@postClaimTarget');
});

/**************************************************************************************************
    Comments
**************************************************************************************************/
Route::group(['prefix' => 'comments', 'namespace' => 'Comments'], function () {
    Route::post('make/{model}/{id}', 'CommentController@store');
    Route::delete('{comment}', 'CommentController@destroy')->name('comments.destroy')->where('comment', '[0-9]+');
    Route::post('edit/{comment}', 'CommentController@update')->name('comments.update');
    Route::post('{comment}', 'CommentController@reply')->name('comments.reply');
    Route::post('{id}/feature', 'CommentController@feature')->name('comments.feature');
    Route::post('{id}/like/{action}', 'CommentController@like')->name('comments.like');
    Route::get('liked', 'CommentController@getLikedComments');
});

Route::group(['prefix' => 'collection', 'namespace' => 'Users'], function () {
    Route::get('/', 'CollectionController@getIndex');
    Route::get('complete/{id}', 'CollectionController@getCompleteCollection');
    Route::post('complete/{id}', 'CollectionController@postCompleteCollection');
});

/**************************************************************************************************
    Friends
**************************************************************************************************/
Route::group(['prefix' => 'friends', 'namespace' => 'Users'], function () {
    Route::get('/', 'FriendController@getIndex');
    Route::get('requests', 'FriendController@getFriendRequests');
    Route::post('requests/{id}', 'FriendController@sendFriendRequest');
    Route::post('requests/{id}/{accept}', 'FriendController@postAcceptRequest');
    // remove friend
    Route::post('remove/{id}', 'FriendController@postRemoveFriend');
    // block friend
    Route::post('block/{id}', 'FriendController@postBlockUser');
});

/**************************************************************************************************
    Criteria
**************************************************************************************************/
Route::group(['prefix' => 'criteria'], function () {
    Route::get('{entity}/{id}', 'CriterionController@getCriterionSelector')->where('entity', 'prompt|gallery');
    Route::get('{entity}/{id}/{entity_id}/{form_id}', 'CriterionController@getCriterionForm')->where('entity', 'prompt|gallery');
    Route::get('{id}', 'CriterionController@getCriterionFormLimited');
    Route::post('rewards/{id}', 'CriterionController@postCriterionRewards');

    Route::get('guide/{id}', 'CriterionController@getCriterionGuide');
});