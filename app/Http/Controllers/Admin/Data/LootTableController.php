<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;

use App\Services\LootService;

use App\Http\Controllers\Controller;

class LootTableController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Loot Table Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of loot tables.
    |
    */

    /**
     * Shows the loot table index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.loot_tables.loot_tables', [
            'tables' => LootTable::paginate(20)
        ]);
    }

    /**
     * Shows the create loot table page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateLootTable()
    {
        $rarities = Item::whereNotNull('data')->get()->pluck('rarity')->unique()->toArray();
        sort($rarities);

        return view('admin.loot_tables.create_edit_loot_table', [
            'table' => new LootTable,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'rarities' => array_filter($rarities),
        ]);
    }

    /**
     * Shows the edit loot table page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLootTable($id)
    {
        $table = LootTable::find($id);
        if(!$table) abort(404);

        $rarities = Item::whereNotNull('data')->get()->pluck('rarity')->unique()->toArray();
        sort($rarities);

        return view('admin.loot_tables.create_edit_loot_table', [
            'table' => $table,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'rarities' => array_filter($rarities),
        ]);
    }

    /**
     * Creates or edits a loot table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\LootService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditLootTable(Request $request, LootService $service, $id = null)
    {
        $id ? $request->validate(LootTable::$updateRules) : $request->validate(LootTable::$createRules);
        $data = $request->only([
            'name', 'display_name', 'rewardable_type', 'rewardable_id', 'quantity', 'weight',
            'criteria', 'rarity'
        ]);
        if($id && $service->updateLootTable(LootTable::find($id), $data)) {
            flash('Loot table updated successfully.')->success();
        }
        else if (!$id && $table = $service->createLootTable($data)) {
            flash('Loot table created successfully.')->success();
            return redirect()->to('admin/data/loot-tables/edit/'.$table->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the loot table deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteLootTable($id)
    {
        $table = LootTable::find($id);
        return view('admin.loot_tables._delete_loot_table', [
            'table' => $table,
        ]);
    }

    /**
     * Deletes an item category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\LootService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteLootTable(Request $request, LootService $service, $id)
    {
        if($id && $service->deleteLootTable(LootTable::find($id))) {
            flash('Loot table deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/loot-tables');
    }

    /**
     * Gets the loot table test roll modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\LootService  $service
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollLootTable(Request $request, LootService $service, $id)
    {
        $table = LootTable::find($id);
        if(!$table) abort(404);

        // Normally we'd merge the result tables, but since we're going to be looking at
        // the results of each roll individually on this page, we'll keep them separate
        $results = [];
        for ($i = 0; $i < $request->get('quantity'); $i++)
            $results[] = $table->roll();

        return view('admin.loot_tables._roll_loot_table', [
            'table' => $table,
            'results' => $results,
            'quantity' => $request->get('quantity')
        ]);
    }
}
