@php
    // This file represents a common source and definition for assets used in loot_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $characterCurrencies = \App\Models\Currency\Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id');
    $items = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $pets = \App\Models\Pet\Pet::orderBy('name')->pluck('name', 'id');
    $variants = \App\Models\Pet\PetVariant::orderBy('variant_name')->pluck('variant_name', 'id')
    ->map(function ($variant, $key) {
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

<div id="lootRowData" class="hide">
    <table class="table table-sm">
        <tbody id="lootRow">
            <tr class="loot-row">
                <td>
                    {!! Form::select('rewardable_type[]', ['Item' => 'Item', 'Currency' => 'Currency', 'Pet' => 'Pet', 'PetVariant' => 'Pet Variant'] + ($showLootTables ? ['LootTable' => 'Loot Table'] : []) + ($showRaffles ? ['Raffle' => 'Raffle Ticket'] : []) + (isset($showThemes) && $showThemes ? ['Theme' => 'Theme'] : []) + ((isset($showRecipes) && $showRecipes) ? ['Recipe' => 'Recipe'] : []), null, ['class' => 'form-control reward-type', 'placeholder' => 'Select Reward Type']) !!}
                </td>
                <td class="loot-row-select"></td>
                <td>{!! Form::text('quantity[]', 1, ['class' => 'form-control']) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('rewardable_id[]', $items, null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('rewardable_id[]', $currencies, null, ['class' => 'form-control currency-select', 'placeholder' => 'Select Currency']) !!}
    {!! Form::select('rewardable_id[]', $pets, null, ['class' => 'form-control pet-select', 'placeholder' => 'Select Pet']) !!}
    {!! Form::select('rewardable_id[]', $variants, null, ['class' => 'form-control pet-variant-select', 'placeholder' => 'Select Pet Variant']) !!}
    @if ($showLootTables)
        {!! Form::select('rewardable_id[]', $tables, null, ['class' => 'form-control table-select', 'placeholder' => 'Select Loot Table']) !!}
    @endif
    @if ($showRaffles)
        {!! Form::select('rewardable_id[]', $raffles, null, ['class' => 'form-control raffle-select', 'placeholder' => 'Select Raffle']) !!}
    @endif
    @if (isset($showThemes) && $showThemes)
        {!! Form::select('rewardable_id[]', $themes, null, ['class' => 'form-control theme-select', 'placeholder' => 'Select Theme']) !!}
    @endif
    @if (isset($showRecipes) && $showRecipes)
        {!! Form::select('rewardable_id[]', $recipes, null, ['class' => 'form-control recipe-select', 'placeholder' => 'Select Recipe']) !!}
    @endif
</div>
