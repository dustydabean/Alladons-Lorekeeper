@extends('home.trades.listings.layout')

@section('trade-title') Trades @endsection

@section('trade-content')
{!! breadcrumbs(['Trades' => 'trades/open', 'Listings' => 'trades/listings', 'New Listing' => 'trades/listings/create']) !!}

<h1>
    New Trade Listing
</h1>

<p>
    Create a new trade listing. 
    <strong>Some notes:</strong>
    <ul>
        <li>You can't modify the listing after its creation, so make sure everything is in order!</li> 
        <li>Note that you may only add up to <strong>{{ Config::get('lorekeeper.settings.trade_asset_limit') }}</strong> things to each side (seeking/offering) of a listing-- if necessary, please create a new listing to add more.</li> 
        <li><strong>Note that this does not interact automatically with the trade system;</strong> while trade listings should <strong>not</strong> be tentative, including an item or character you own within a listing will not inherently do anything with the item/character(s), and you will need to add them to trade(s) on your own.</li> 
        <ul><li>Traded items/characters are not automatically removed from a listing.</li></ul>
        <li>Listings expire after {{ $listingDuration }} days. You will also be able to manually mark a listing as expired before that point.</li>
    </ul>
</p>

{!! Form::open(['url' => 'trades/listings/create']) !!}

    <div class="form-group">
        {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('This comment will be displayed on the trade index. You can write a helpful note here, for example to note down the purpose of the trade.') !!}
        {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('contact', 'Preferred Method(s) of Contact') !!} 
        {!! add_help('Enter in your preferred method(s) of contact. This field cannot be left blank.') !!} 
        {!! Form::text('contact', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <h2>Seeking <a class="small inventory-collapse-toggle collapse-toggle" href="#userSeeking" data-toggle="collapse">Show</a></h2>
    <div class="mb-3 collapse" id="userSeeking">
    <p>Select the items, currencies, and/or other goods or services you're seeking.</p>
        <h3>Items</h3>
        <div class="form-group">
            {!! Form::label('Item(s)') !!} {!! add_help('The quantity of any selected items must be at least 1.') !!}
            <div id="itemList">
                <div class="d-flex mb-2">
                    {!! Form::select('item_ids[]', $items, null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                    {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                    <a href="#" class="remove-item btn btn-danger mb-2 disabled">×</a>
                </div>
            </div>
            <div><a href="#" class="btn btn-primary" id="add-item">Add Item</a></div>
            <div class="item-row hide mb-2">
                {!! Form::select('item_ids[]', $items, null, ['class' => 'form-control mr-2 item-select', 'placeholder' => 'Select Item']) !!}
                {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                <a href="#" class="remove-item btn btn-danger mb-2">×</a>
            </div>
        </div>
        @if(isset($currencies) && $currencies)
        <h3>Currencies</h3>
            @foreach($currencies as $currency)
                <div class="form-group">
                    {!! Form::checkbox('seeking_currency_ids[]', $currency->id, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('seeking_currency_ids[]', $currency->name, ['class' => 'form-check-label ml-3']) !!} {!! add_help('Toggle this on to mark this currency as being sought.') !!}
                </div>
            @endforeach
        @endif
        <h3>Other</h3>
        <div class="form-group">
            {!! Form::label('seeking_etc', 'Other Goods or Services') !!} 
            {!! add_help('Enter in any goods/services you are seeking that are not handled by the site-- for example, art. This should be brief!') !!} 
            {!! Form::text('seeking_etc', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <h2>Offering <a class="small inventory-collapse-toggle collapse-toggle" href="#userOffering" data-toggle="collapse">Show</a></h2>
    <div class="mb-3 collapse" id="userOffering">
    <p>Select the items, characters, currencies, and/or other goods or services you're offering.</p>
        @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => [], 'page' => $page])
        @include('widgets._my_character_select', ['readOnly' => true, 'categories' => $characterCategories])
        @if(isset($currencies) && $currencies)
        <h3>Currencies</h3>
            @foreach($currencies as $currency)
                <div class="form-group">
                    {!! Form::checkbox('offer_currency_ids[]', $currency->id, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('offer_currency_ids[]', $currency->name, ['class' => 'form-check-label ml-3']) !!} {!! add_help('Toggle this on to mark this currency as offered.') !!}
                </div>
            @endforeach
        @endif
        <h3>Other</h3>
        <div class="form-group">
            {!! Form::label('offering_etc', 'Other Goods or Services') !!} 
            {!! add_help('Enter in any goods/services you are offerering that are not handled by the site-- for example, art. This should be brief!') !!} 
            {!! Form::text('offering_etc', null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="text-right">{!! Form::submit('Create Listing', ['class' => 'btn btn-primary']) !!}</div>
{!! Form::close() !!}

@endsection
@section('scripts')
    @parent
    @include('widgets._inventory_select_js', ['readOnly' => true])
    @include('widgets._my_character_select_js', ['readOnly' => true])
    <script>
        $(document).ready(function() {
            $('.default.item-select-row').selectize();

            $('#add-item').on('click', function(e) {
                e.preventDefault();
                addItemRow();
            });
            $('.remove-item').on('click', function(e) {
                e.preventDefault();
                removeItemRow($(this));
            });

            function addItemRow() {
                var $rows = $("#itemList > div")
                if($rows.length === 1) {
                    $rows.find('.remove-item').removeClass('disabled')
                }
                var $clone = $('.item-row').clone();
                $('#itemList').append($clone);
                $clone.removeClass('hide item-row');
                $clone.addClass('d-flex');
                $clone.find('.remove-item').on('click', function(e) {
                    e.preventDefault();
                    removeItemRow($(this));
                })
                $clone.find('.item-select-row').selectize();
            }
            function removeItemRow($trigger) {
                $trigger.parent().remove();
                var $rows = $("#itemList > div")
                if($rows.length === 1) {
                    $rows.find('.remove-item').addClass('disabled')
                }
            }
        });
    </script>
@endsection