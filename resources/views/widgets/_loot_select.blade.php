@php
    // This file represents a common source and definition for assets used in loot_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $characterCurrencies = \App\Models\Currency\Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id');
    $items = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $pets = \App\Models\Pet\Pet::orderBy('name')->pluck('name', 'id');
    $variants = \App\Models\Pet\PetVariant::orderBy('variant_name')->pluck('variant_name', 'id')->map(function ($variant, $key) {
        $pet = \App\Models\Pet\PetVariant::find($key)->pet;
        return $variant . ' (' . $pet->name . ' variant)';
    });
    $currencies = \App\Models\Currency\Currency::where('is_user_owned', 1)
        ->orderBy('name')
        ->pluck('name', 'id');
    if ($showLootTables) {
        $tables = \App\Models\Loot\LootTable::orderBy('name')->pluck('name', 'id');
    }
    if ($showRaffles) {
        $raffles = \App\Models\Raffle\Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id');
    }
    if (isset($showRecipes) && $showRecipes) {
        $recipes = \App\Models\Recipe\Recipe::orderBy('name')->pluck('name', 'id');
    }
    if (isset($showThemes) && $showThemes) {
        $themes = \App\Models\Theme::orderBy('name')
            ->where('is_user_selectable', 0)
            ->pluck('name', 'id');
    }
@endphp

<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLoot">Add Reward</a>
</div>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="35%">Reward Type</th>
            <th width="35%">Reward</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if ($loots)
            @foreach ($loots as $loot)
                <tr class="loot-row">
                    <td>
                        {!! Form::select('rewardable_type[]',
                        ['Item' => 'Item', 'Currency' => 'Currency', 'Pet' => 'Pet', 'PetVariant' => 'Pet Variant'] + ($showLootTables ? ['LootTable' => 'Loot Table'] : []) + ($showRaffles ? ['Raffle' => 'Raffle Ticket'] : []) + (isset($showThemes) && $showThemes ? ['Theme' => 'Theme'] : []) + ((isset($showRecipes) && $showRecipes) ? ['Recipe' => 'Recipe'] : []), $loot->rewardable_type,
                        ['class' => 'form-control reward-type', 'placeholder' => 'Select Reward Type']) !!}
                    </td>

                    <td class="loot-row-select">
                        @if ($loot->rewardable_type == 'Item')
                            {!! Form::select('rewardable_id[]', $items, $loot->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($loot->rewardable_type == 'Currency')
                            {!! Form::select('rewardable_id[]', $currencies, $loot->rewardable_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @elseif($loot->rewardable_type == 'Pet')
                            {!! Form::select('rewardable_id[]', $pets, $loot->rewardable_id, ['class' => 'form-control pet-select selectize', 'placeholder' => 'Select Pet']) !!}
                        @elseif($loot->rewardable_type == 'PetVariant')
                            {!! Form::select('rewardable_id[]', $variants, $loot->rewardable_id, ['class' => 'form-control pet-variant-select selectize', 'placeholder' => 'Select Pet Variant']) !!}
                        @elseif($showLootTables && $loot->rewardable_type == 'LootTable')
                            {!! Form::select('rewardable_id[]', $tables, $loot->rewardable_id, ['class' => 'form-control table-select selectize', 'placeholder' => 'Select Loot Table']) !!}
                        @elseif($showRaffles && $loot->rewardable_type == 'Raffle')
                            {!! Form::select('rewardable_id[]', $raffles, $loot->rewardable_id, ['class' => 'form-control raffle-select selectize', 'placeholder' => 'Select Raffle']) !!}
                        @elseif(isset($showThemes) && $showThemes && $loot->rewardable_type == 'Theme')
                            {!! Form::select('rewardable_id[]', $themes, $loot->rewardable_id, ['class' => 'form-control theme-select selectize', 'placeholder' => 'Select Theme']) !!}
                        @elseif((isset($showRecipes) && $showRecipes) && $loot->rewardable_type == 'Recipe')
                            {!! Form::select('rewardable_id[]', $recipes, $loot->rewardable_id, ['class' => 'form-control recipe-select selectize', 'placeholder' => 'Select Recipe']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
