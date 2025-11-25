<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Foraging\Forage;

use App\Services\ForageService;

use App\Http\Controllers\Controller;

class ForageController extends Controller
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
        return view('admin.foraging.index', [
            'tables' => Forage::paginate(20)
        ]);
    }
    
    /**
     * Shows the create loot table page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateForage()
    {
        return view('admin.foraging.create_edit_forage', [
            'table' => new Forage,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'forage_currencies' => ['None' => 'None'] + Currency::orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }
    
    /**
     * Shows the edit loot table page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditForage($id)
    {
        $table = Forage::find($id);
        if(!$table) abort(404);
        return view('admin.foraging.create_edit_forage', [
            'table' => $table,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'forage_currencies' => ['None' => 'None'] + Currency::orderBy('name')->pluck('name', 'id')->toArray(),
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
    public function postCreateEditForage(Request $request, ForageService $service, $id = null)
    {
        $id ? $request->validate(Forage::$updateRules) : $request->validate(Forage::$createRules);
        $data = $request->only([
            'name', 'display_name', 'rewardable_type', 'rewardable_id', 'quantity', 'weight', 'is_active', 'image',
            'is_active', 'active_until', 'stamina_cost', 'has_cost', 'currency_id', 'currency_quantity'
        ]);
        if($id && $service->updateForage(Forage::find($id), $data)) {
            flash('Forage updated successfully.')->success();
        }
        else if (!$id && $table = $service->createForage($data)) {
            flash('Forage created successfully.')->success();
            return redirect()->to('admin/data/forages/edit/'.$table->id);
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
    public function getDeleteForage($id)
    {
        $table = Forage::find($id);
        return view('admin.foraging._delete_forage', [
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
    public function postDeleteForage(Request $request, ForageService $service, $id)
    {
        if($id && $service->deleteForage(Forage::find($id))) {
            flash('Forage deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/forages');
    }
    
    /**
     * Gets the loot table test roll modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\LootService  $service
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollForage(Request $request, ForageService $service, $id)
    {
        $table = Forage::find($id);
        if(!$table) abort(404);

        // Normally we'd merge the result tables, but since we're going to be looking at
        // the results of each roll individually on this page, we'll keep them separate
        $results = [];
        for ($i = 0; $i < $request->get('quantity'); $i++)
            $results[] = $table->roll();

        return view('admin.foraging._roll_table', [
            'table' => $table,
            'results' => $results,
            'quantity' => $request->get('quantity')
        ]);
    }
}
