<h3>Seeking:</h3>
        
<div class="card mb-3 trade-offer">
    @if(isset($data) && $data['items'])
        <div class="card-header">
            Items
        </div>
        <div class="card-body user-items">
            <table class="table table-sm">
                <thead class="thead-light">
                    <tr class="d-flex">
                        <th class="col">Item</th>
                        <th class="col">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['items'] as $itemRow)
                        <tr class="d-flex">
                            <td class="col-6">@if(isset($itemRow['asset']->image_url)) <img class="small-icon" src="{{$itemRow['asset']->image_url }}"> @endif
                            <a href="/world/items?name={{ $itemRow['asset']->name }}">{!! $itemRow['asset']->name !!}</a></td>
                            <td class="col-6">{!! $itemRow['quantity'] !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    @if(isset($data) && $data['currencies'])
        <div class="card-header border-top border-bottom-0">
            Currencies
        </div>
        <ul class="list-group list-group-flush">
            @foreach($data['currencies'] as $currency)
                <li class="list-group-item border-bottom-0 border-top currency-item">
                    {!! $currency['asset']->display('') !!}
                </li>
            @endforeach
        </ul>
    @endif
    @if(isset($listing->data['seeking_etc']) && $listing->data['seeking_etc'])
        <div class="card-header border-top border-bottom-0">
            Other Goods & Services
        </div>
        <ul class="list-group list-group-flush">
            <div class="card-body">
            {!! nl2br(htmlentities($listing->data['seeking_etc'])) !!}
            </div>
        </ul>
    @endif
    @if(!isset($listing->data['seeking']) && !isset($listing->data['seeking_etc']))
        <div class="card-body">{!! $listing->user->displayName !!} is not seeking anything.</div>
    @endif
</div>