<div id="recycleRowData" class="hide">
    <table class="table table-sm">
        <tbody id="recyclableRow">
            <tr class="recyclable-row">
                <td>{!! Form::select('recyclable_type[]', ['Item' => 'Item', 'ItemCategory' => 'Item Category'], null, ['class' => 'form-control recyclable-type', 'placeholder' => 'Select Recyclable Type']) !!}</td>
                <td class="recyclable-row-select"></td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-recyclable-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('recyclable_id[]', $items, null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('recyclable_id[]', $categories, null, ['class' => 'form-control category-select', 'placeholder' => 'Select Item Category']) !!}
</div>
@include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'showLootTables' => true, 'showRaffles' => true])