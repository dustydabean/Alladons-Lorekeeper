@php
    // This file represents a common source and definition for assets used in loot_select
    // While it is not per se as tidy as defining these in the controller(s),
    // doing so this way enables better compatibility across disparate extensions
    $items = \App\Models\Item\Item::orderBy('name')->pluck('name', 'id');
    $currencies = \App\Models\Currency\Currency::where('is_user_owned', 1)
        ->orderBy('name')
        ->pluck('name', 'id');
    $tables = \App\Models\Loot\LootTable::orderBy('name')->pluck('name', 'id');
@endphp

<div data-id="{{ $group }}">
    <div class="text-right mb-3">
        <a href="#" class="btn btn-outline-info addLoot">Add {{ ucfirst($label) }} Reward</a>
    </div>
    <table class="table table-sm lootTable">
        <thead>
            <tr>
                <th width="25%">{{ ucfirst($label) }} Reward Type</th>
                <th width="25%">{{ ucfirst($label) }} Reward</th>
                <th width="20%">Minimum Quantity</th>
                <th width="20%">Maximum Quantity</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody class="lootTableBody">
            @if ($loots)
                @foreach ($loots as $loot)
                    <tr class="loot-row">
                        <td>{!! Form::select('rewardable_type[' . $group . '][]', ['Item' => 'Item', 'Currency' => 'Currency', 'LootTable' => 'Loot Table'], $loot->rewardable_type, ['class' => 'form-control reward-type', 'placeholder' => 'Select Reward Type']) !!}</td>
                        <td class="loot-row-select">
                            @if ($loot->rewardable_type == 'Item')
                                {!! Form::select('rewardable_id[' . $group . '][]', $items, $loot->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                            @elseif($loot->rewardable_type == 'Currency')
                                {!! Form::select('rewardable_id[' . $group . '][]', $currencies, $loot->rewardable_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                            @elseif($loot->rewardable_type == 'LootTable')
                                {!! Form::select('rewardable_id[' . $group . '][]', $tables, $loot->rewardable_id, ['class' => 'form-control table-select selectize', 'placeholder' => 'Select Loot Table']) !!}
                            @endif
                        </td>
                        <td>{!! Form::text('min_quantity[' . $group . '][]', $loot->min_quantity, ['class' => 'form-control min-quantity']) !!}</td>
                        <td>{!! Form::text('max_quantity[' . $group . '][]', $loot->max_quantity, ['class' => 'form-control max-quantity']) !!}</td>
                        <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
