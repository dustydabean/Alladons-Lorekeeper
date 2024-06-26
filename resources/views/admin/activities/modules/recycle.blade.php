<h4>Items that can be recycled</h4>
<p>The Recycle module allows users to submit items from their inventory as defined here.</p>
<div class="text-right mb-3">
    <a href="#" class="btn btn-sm btn-primary" id="addRecyclable">Add Recyclable Items</a>
</div>
<table class="table table-sm" id="recyclableTable">
    <thead>
        <tr>
            <th>Item or Category</th>
            <th>Type</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="recyclable">
    @if($settings && $settings->recyclables)
        @foreach($settings->recyclables as $recyclable)
             <tr class="recyclable-row">
                <td>{!! Form::select('recyclable_type[]', ['Item' => 'Item', 'ItemCategory' => 'Item Category'], $recyclable->rewardable_type, ['class' => 'form-control recyclable-type', 'placeholder' => 'Select Recyclable Type']) !!}</td>
                <td class="recyclable-row-select">
                    @if($recyclable->rewardable_type == 'Item')
                        {!! Form::select('recyclable_id[]', $items, $recyclable->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                    @elseif($recyclable->rewardable_type == 'ItemCategory')
                        {!! Form::select('recyclable_id[]', $categories, $recyclable->rewardable_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item Category']) !!}
                    @endif
                </td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-recyclable-button">Remove</a></td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

<div class="form-group w-50 mt-5">
    {!! Form::label('Items Required') !!} {!! add_help('How many items are required to turn in for the reward.') !!}
    {!! Form::number('recyclableQuantity', $settings->quantity[0] ?? null, ['class' => 'form-control']) !!}
</div>

<h4 class="mt-5">Reward</h4>
The reward users will recieve upon submitting the items from their inventory
@include('widgets._loot_select', ['loots' => $settings->loot ?? null, 'showLootTables' => true, 'showRaffles' => true])