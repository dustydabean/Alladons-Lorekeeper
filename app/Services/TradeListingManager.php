<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Auth;
use Settings;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Character\Character;
use App\Models\Character\CharacterTransfer;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\TradeListing;

class TradeListingManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Trade Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of trade data.
    |
    */

    /**
     * Creates a new trade listing.
     *
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @return bool|\App\Models\TradeListing
     */
    public function createTradeListing($data, $user)
    {
        DB::beginTransaction();
        try {
            if(!isset($data['contact'])) throw new \Exception("Please enter your preferred method(s) of contact.");

            $listing = TradeListing::create([
                'title' => isset($data['title']) ? $data['title'] : null,
                'user_id' => $user->id,
                'comments' => isset($data['comments']) ? $data['comments'] : null,
                'contact' => $data['contact'],
                'data' => null
            ]);

            if($assetData = $this->handleListingAssets($listing, $data, $user)) {
                $listingData = $listing->data;
                $listingData['offering'] = getDataReadyAssets($assetData['offering']);
                $listingData['offering'] <> null ? $listing->data = json_encode($listingData) : null;
                $listing->save();
            }

            if($assetData = $this->handleSeekingAssets($listing, $data, $user)) {
                $listingData = $listing->data;
                $listingData['seeking'] = getDataReadyAssets($assetData['seeking']);
                $listingData['seeking'] <> null ? $listing->data = json_encode($listingData) : null;
                $listing->save();
            }

            if($data['offering_etc'] || $data['seeking_etc']) {
                $listingData = $listing->data;
                $listingData['offering_etc'] = $data['offering_etc'];
                $listingData['seeking_etc'] = $data['seeking_etc'];
                $listing->data = json_encode($listingData);
                $listing->save();
            }

            // These checks are performed here, since it's faster and easier to check for the asset arrays (vs the separate inputs)
            if(!$listing->data) throw new \Exception("Please enter what you're seeking and offering.");
            if(!isset($listing->data['seeking']) && !isset($listing->data['seeking_etc'])) throw new \Exception("Please enter what you're seeking.");
            if(!isset($listing->data['offering']) && !isset($listing->data['offering_etc'])) throw new \Exception("Please enter what you're offering.");

            $duration = Settings::get('trade_listing_duration');
            $listing->expires_at = Carbon::now()->addDays($duration);
            $listing->save();

            return $this->commitReturn($listing);

        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Marks a trade listing as expired.
     *
     * @param  array                        $data
     * @param  \App\Models\User\User        $user
     * @return bool|\App\Models\TradeListing
     */
    public function markExpired($data, $user)
    {
        DB::beginTransaction();
        try {
            $listing = TradeListing::find($data['id']);
            if(!$listing) throw new \Exception("Invalid trade listing.");
            if(!$listing->isActive) throw new \Exception("This listing is already expired.");
            if(!$listing->user->id == Auth::user()->id && !Auth::user()->hasPower('manage_submissions')) throw new \Exception("You can't edit this listing.");

            $listing->expires_at = Carbon::now();
            $listing->save();

            return $this->commitReturn($listing);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handles recording of assets on the seeking side of a trade listing, as well as initial validation.
     *
     * @param  \App\Models\TradeListing $listing
     * @param  array                    $data
     * @return bool|array
     */
    private function handleSeekingAssets($listing, $data, $user)
    {
        DB::beginTransaction();
        try {
            $seekingAssets = createAssetsArray();
            $assetCount = 0;
            $assetLimit = Config::get('lorekeeper.settings.trade_asset_limit');

            if(isset($data['item_ids'])) {
                $keyed_quantities = [];
                array_walk($data['item_ids'], function($id, $key) use(&$keyed_quantities, $data) {
                    if($id != null && !in_array($id, array_keys($keyed_quantities), TRUE)) {
                        $keyed_quantities[$id] = $data['quantities'][$key];
                    }
                });

                // Elaborate validation to account for the nature of the item select form.
                foreach($data['item_ids'] as $id) {
                    if($id != null) {
                    $item = Item::find($id);
                    if(!$item) throw new \Exception("One or more of the selected items is invalid.");
                    }
                }
                $items = Item::find($data['item_ids']);

                foreach($items as $item) {
                    if(!$item) throw new \Exception("Invalid item selected.");
                    if(!$item->allow_transfer) throw new \Exception("One or more of the selected items cannot be transferred.");

                    addAsset($seekingAssets, $item, $keyed_quantities[$item->id]);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a listing.");

            if(isset($data['seeking_currency_ids'])) {
                $currencies = Currency::find($data['seeking_currency_ids']);

                foreach($currencies as $currency) {
                    if(!$currency) throw new \Exception("Invalid currency selected.");
                    if(!$currency->is_user_owned) throw new \Exception("One or more of the selected currencies cannot be held by users.");
                    if(!$currency->allow_user_to_user) throw new \Exception("One or more of the selected currencies cannot be traded.");

                    addAsset($seekingAssets, $currency, 1);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a listing.");

            return $this->commitReturn(['seeking' => $seekingAssets]);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handles recording of assets on the user's side of a trade listing, as well as initial validation.
     *
     * @param  \App\Models\TradeListing $listing
     * @param  array                    $data
     * @param  \App\Models\User\User    $user
     * @return bool|array
     */
    private function handleListingAssets($listing, $data, $user)
    {
        DB::beginTransaction();
        try {
            $userAssets = createAssetsArray();
            $assetCount = 0;
            $assetLimit = Config::get('lorekeeper.settings.trade_asset_limit');

            // Attach items. They are not even held, merely recorded for display on the listing.
            if(isset($data['stack_id'])) {
                foreach($data['stack_id'] as $key=>$stackId) {
                    $stack = UserItem::with('item')->find($stackId);
                    if(!$stack || $stack->user_id != $user->id) throw new \Exception("Invalid item selected.");
                    if(!$stack->item->allow_transfer || isset($stack->data['disallow_transfer'])) throw new \Exception("One or more of the selected items cannot be transferred.");

                    addAsset($userAssets, $stack, $data['stack_quantity'][$key]);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a listing.");

            // Attach currencies. Character currencies cannot be attached to trades, so we're just checking the user's bank.
            if(isset($data['offer_currency_ids'])) {
                foreach($data['offer_currency_ids'] as $key=>$currencyId) {
                    $currency = Currency::where('allow_user_to_user', 1)->where('id', $currencyId)->first();
                    if(!$currency) throw new \Exception("Invalid currency selected.");

                    addAsset($userAssets, $currency, 1);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a listing.");

            // Attach characters.
            if(isset($data['character_id'])) {
                foreach($data['character_id'] as $characterId) {
                    $character = Character::where('id', $characterId)->where('user_id', $user->id)->first();
                    if(!$character) throw new \Exception("Invalid character selected.");
                    if(!$character->is_sellable && !$character->is_tradeable && !$character->is_giftable) throw new \Exception("One or more of the selected characters cannot be transferred.");
                    if(CharacterTransfer::active()->where('character_id', $character->id)->exists()) throw new \Exception("One or more of the selected characters is already pending a character transfer.");
                    if($character->trade_id) throw new \Exception("One or more of the selected characters is already in a trade.");
                    if($character->designUpdate()->active()->exists()) throw new \Exception("One or more of the selected characters has an active design update. Please wait for it to be processed, or delete it.");
                    if($character->transferrable_at && $character->transferrable_at->isFuture()) throw new \Exception("One or more of the selected characters is still on transfer cooldown and cannot be transferred.");

                    addAsset($userAssets, $character, 1);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a listing.");

            return $this->commitReturn(['offering' => $userAssets]);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
