<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use Illuminate\Support\Arr;
use App\Models\Daily\Daily;
use App\Models\Daily\DailyTimer;
use App\Models\Daily\DailyReward;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use Carbon\Carbon;

class DailyService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Daily Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of dailies.
    |
    */

    /**********************************************************************************************

        DAILIES

    **********************************************************************************************/

    /**
     * Creates a new daily.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Daily\Daily
     */
    public function createDaily($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateDailyData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            
            $buttonImage = null;
            if(isset($data['button_image']) && $data['button_image']) {
                $data['has_button_image'] = 1;
                $buttonImage = $data['button_image'];
                unset($data['button_image']);
            }
            else $data['has_button_image'] = 0;

            $data['is_timed_daily'] = isset($data['is_timed_daily']);

            $daily = Daily::create($data);

            if ($image) $this->handleImage($image, $daily->dailyImagePath, $daily->dailyImageFileName);
            if ($buttonImage) $this->handleImage($buttonImage, $daily->dailyImagePath, $daily->buttonImageFileName);

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $daily);

            return $this->commitReturn($daily);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a daily.
     *
     * @param  \App\Models\Daily\Daily  $daily
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Daily\Daily
     */
    public function updateDaily($daily, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Daily::where('name', $data['name'])->where('id', '!=', $daily->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateDailyData($data, $daily);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            $buttonImage = null;
            if(isset($data['button_image']) && $data['button_image']) {
                $data['has_button_image'] = 1;
                $buttonImage = $data['button_image'];
                unset($data['button_image']);
            }
            
            $data['is_timed_daily'] = isset($data['is_timed_daily']);

            $daily->update($data);

            if ($daily) $this->handleImage($image, $daily->dailyImagePath, $daily->dailyImageFileName);
            if ($buttonImage) $this->handleImage($buttonImage, $daily->dailyImagePath, $daily->buttonImageFileName);

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $daily);

            return $this->commitReturn($daily);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Processes user input for creating/updating a daily.
     *
     * @param  array                  $data
     * @param  \App\Models\Daily\Daily  $daily
     * @return array
     */
    private function populateDailyData($data, $daily = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        $data['is_active'] = isset($data['is_active']);
        $data['is_one_off'] = isset($data['is_one_off']);


        if(isset($data['remove_image']))
        {
            if($daily && $daily->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($daily->dailyImagePath, $daily->dailyImageFileName);
            }
            unset($data['remove_image']);
        }

        if(isset($data['remove_button_image']))
        {
            if($daily && $daily->has_button_image && $data['remove_button_image'])
            {
                $data['has_button_image'] = 0;
                $this->deleteImage($daily->dailyImagePath, $daily->buttonImageFileName);
            }
            unset($data['remove_button_image']);
        }

        return $data;
    }

    /**
     * Processes user input for creating/updating daily rewards.
     *
     * @param  array                      $data
     * @param  \App\Models\Daily\Daily  $daily
     */
    private function populateRewards($data, $daily)
    {
        // Clear the old rewards...
        $daily->rewards()->delete();

        if(isset($data['rewardable_type'])) {
            foreach($data['rewardable_type'] as $key => $type)
            {
                DailyReward::create([
                    'daily_id'       => $daily->id,
                    'rewardable_type' => $type,
                    'rewardable_id'   => $data['rewardable_id'][$key],
                    'quantity'        => $data['quantity'][$key],
                ]);
            }
        }
    }

    /**
     * Deletes a daily.
     *
     * @param  \App\Models\Daily\Daily  $daily
     * @return bool
     */
    public function deleteDaily($daily)
    {
        DB::beginTransaction();

        try {

            if($daily->has_image) $this->deleteImage($daily->dailyImagePath, $daily->dailyImageFileName);
            if($daily->has_button_image) $this->deleteImage($daily->dailyImagePath, $daily->buttonyImageFileName);
            $daily->rewards()->delete();
            $daily->timers()->delete();
            $daily->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts daily order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortDaily($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Daily::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     *  ROLLS
     *
     */


    /**
     * Rolls an item/currency from the daily.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return bool|App\Models\DailyTimer\DailyTimer
     */
    public function rollDaily($data, $user)
    {
        DB::beginTransaction();

        try {

            // Check that the daily exists and is open
            $daily = Daily::where('id', $data['daily_id'])->where('is_active', 1)->first();
            if(!$daily) throw new \Exception("Invalid ".__('dailies.daily')." selected.");

            // Check if the user has not done the daily that day
            if(!$this->canRoll($daily, $user)) throw new \Exception("You have already received your ".__('dailies.daily')." reward.");
            
            //build reward data to the correct format used for grants
            $dailyRewards = $daily->rewards()->get();
            $rewardData = [];
            $rewardData['rewardable_type'] = [];
            $rewardData['rewardable_id'] = [];
            $rewardData['quantity'] = [];

            foreach($dailyRewards as $dailyReward){
                $rewardData['rewardable_type'][] = $dailyReward->rewardable_type;
                $rewardData['rewardable_id'][] = $dailyReward->rewardable_id;
                $rewardData['quantity'][] = $dailyReward->quantity;
            }

            // Get the updated set of rewards
            $rewards = $this->processRewards($rewardData);

            //get daily timer now that we know we can roll. if none exists, create one.
            $dailyTimer = DailyTimer::where('daily_id', $daily->id)->where('user_id', $user->id)->first();

            if(!$dailyTimer){
                $dailyTimer = DailyTimer::create([
                    'daily_id' => $daily->id,
                    'user_id' => $user->id,
                    'rolled_at' => Carbon::now()
                ]);
            } else {
                $dailyTimer->rolled_at = Carbon::now();
            }


            // Distribute user rewards
            $logType = __('dailies.daily').' Rewards';
            $dailyData = [
                'data' => 'Received rewards for '.__('dailies.daily').' (<a href="'.$daily->viewUrl.'">#'.$daily->id.'</a>)'
            ];

            $assets = fillUserAssets($rewards, null, $user, $logType, $dailyData);
            if(!$assets) throw new \Exception("Failed to distribute rewards to user.");

            //save the updated or new timer once the rewards were successfully distributed
            $dailyTimer->save();
            $this->commitReturn($dailyTimer);
            return $assets;
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        $this->rollbackReturn(false);
        return null;
    }


    /**
     * Checks if user can roll the daily.
     *
     * @param  \App\Models\Daily\DailyTimer  $dailyTimer
     * @param  \App\Models\User\User      $user
     * @return bool
     */
    public function canRoll($daily, $user)
    {
        $reset = date("Y-m-d H:i:s", strtotime('midnight'));
        
        $dailyTimer = DailyTimer::where('daily_id', $daily->id)->where('user_id', $user->id)->first();

        if($daily->is_one_off){
            // if one off: if a timer exists never roll again
            return !isset($dailyTimer);
        } else {
            // daily daily
            if($dailyTimer && $dailyTimer->rolled_at >= $reset){
                // if a timer exists we cannot roll again
                return false;
            } else {
                // if no timer exists we can roll
                return true;
            }
        }


    }


    /**
     * Processes reward data into a format that can be used for distribution.
     *
     * @param  array $data
     * @param  bool  $isCharacter
     * @param  bool  $isStaff
     * @return array
     */
    private function processRewards($data)
    {

        $assets = createAssetsArray(false);
            // Process the additional rewards
        if(isset($data['rewardable_type']) && $data['rewardable_type'])
        {
            foreach($data['rewardable_type'] as $key => $type)
            {
                $reward = null;
                switch($type)
                {
                    case 'Item':
                        $reward = Item::find($data['rewardable_id'][$key]);
                        break;
                    case 'Currency':
                        $reward = Currency::find($data['rewardable_id'][$key]);
                        if(!$reward->is_user_owned) throw new \Exception("Invalid currency selected.");
                        break;
                    case 'LootTable':
                        $reward = LootTable::find($data['rewardable_id'][$key]);
                        break;
                    case 'Raffle':
                        $reward = Raffle::find($data['rewardable_id'][$key]);
                        break;
                    //uncomment if you use pets or awards, may still have to fix/add in other places
                    /**case 'Pet':
                        $reward = Pet::find($data['rewardable_id'][$key]);
                    case 'Award':
                        $reward = Award::find($data['rewardable_id'][$key]);
                        break;**/
                }
                if(!$reward) continue;
                addAsset($assets, $reward, $data['quantity'][$key]);
            }
        }
        return $assets;
        
    }

}