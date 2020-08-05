@if($data)
    @if($data['items'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Items:</strong>
        </div>
            <div class="col-md">
                <div class="row">   
                    @foreach($data['items'] as $itemRow)
                    <div class="col-sm-4">
                        <a href="/world/items?name={{ $itemRow['asset']->name }}">{!! $itemRow['asset']->name !!}</a> x{!! $itemRow['quantity'] !!}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if($data['currencies'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Currencies:</strong>
        </div>
            <div class="col-md">
                <div class="row">
                    @foreach($data['currencies'] as $currency)
                        <div class="col-sm-3">
                            {!! $currency['asset']->display('') !!}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if(isset($listing->data['seeking_etc']) && $listing->data['seeking_etc'])
        <div class="row">
        <div class="col-sm-2">
            <strong>Other:</strong>
        </div>
            <div class="col-md">
                {!! nl2br(htmlentities($listing->data['seeking_etc'])) !!}
            </div>
        </div>
    @endif
@endif