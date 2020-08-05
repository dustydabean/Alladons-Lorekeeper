
<h3>Offering:</h3>

<div class="card mb-3 trade-offer">
    @if(isset($data) && $data['user_items'])
        <div class="card-header">
            Items
        </div>
        <table class="table table-sm">
            <thead class="thead">
                    <tr class="d-flex">
                        <th class="col-2">Item</th>
                        <th class="col-4">Source</th>
                        <th class="col-4">Notes</th>
                        <th class="col-2">Quantity</th>
                    </tr>
            </thead>
            <tbody>
                @foreach($data['user_items'] as $itemRow)
                    <tr class="d-flex">
                        <td class="col-2">@if(isset($itemsrow[$itemRow['asset']->item_id]->image_url)) <img class="small-icon" src="{{ $itemsrow[$itemRow['asset']->item_id]->image_url }}"> @endif {!! $itemsrow[$itemRow['asset']->item_id]->name !!}
                        <td class="col-4">{!! array_key_exists('data', $itemRow['asset']->data) ? ($itemRow['asset']->data['data'] ? $itemRow['asset']->data['data'] : 'N/A') : 'N/A' !!}</td>
                        <td class="col-4">{!! array_key_exists('notes', $itemRow['asset']->data) ? ($itemRow['asset']->data['notes'] ? $itemRow['asset']->data['notes'] : 'N/A') : 'N/A' !!}</td>
                        <td class="col-2">{!! $itemRow['quantity'] !!}
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @if(isset($data) && $data['characters'])
        <div class="card-header border-top">
            Characters
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($data['characters'] as $character)
                    <div class="col-lg-2 col-sm-3 col-6 mb-3">
                        <div class="text-center inventory-item">
                            <div class="mb-1">
                                <a class="inventory-stack"><img src="{{ $character['asset']->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                            </div>
                            <div>
                                <a class="inventory-stack inventory-stack-name">{!! $character['asset']->displayName !!}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if(isset($data) && $data['currencies'])
        <div class="card-header border-top border-bottom-0">
            Currencies
        </div>
        <ul class="list-group list-group-flush">
            @foreach($data['currencies'] as $currency)
                <li class="list-group-item border-bottom-0 border-top currency-item">
                    {!! $currency['asset'] !!}
                </li>
            @endforeach
        </ul>
    @endif
    @if(isset($listing->data['offering_etc']) && $listing->data['offering_etc'])
        <div class="card-header border-top border-bottom-0">
            Other Goods & Services
        </div>
        <ul class="list-group list-group-flush">
            {!! nl2br(htmlentities($listing->data['offering_etc'])) !!}
        </ul>
    @endif
    @if(!isset($data['offering']) && !isset($listing->data['offering_etc']))
        <div class="card-body">{!! $user->displayName !!} has not offered anything.</div>
    @endif
</div>