<?php namespace App\Services\Item;

use App\Services\Service;

use DB;

use App\Services\InventoryManager;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Shop\Shop;

class CouponService extends Service
{

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [
            'shops' => ['0' => 'Select Shop'] + Shop::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format for edits.
     *
     * @param  string  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        //fetch data from DB, if there is no data then set to NULL instead
        $couponData = [];
        $couponData['shops']

        return $couponData;
    }



}