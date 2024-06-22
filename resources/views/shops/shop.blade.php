@extends('shops.layout')

@section('shops-title')
    {{ $shop->name }}
@endsection

@section('shops-content')
    <x-admin-edit title="Shop" :object="$shop" />
    {!! breadcrumbs(['Shops' => 'shops', $shop->name => $shop->url]) !!}

    <h1>
        {{ $shop->name }}
    </h1>

    @if ($shop->use_coupons)
        <div class="alert alert-success">You can use coupons in this store!</div>
        @if ($shop->allowed_coupons && count(json_decode($shop->allowed_coupons, 1)))
            <div class="alert alert-info">You can use the following coupons: @foreach ($shop->allAllowedCoupons as $coupon)
                    {!! $coupon->displayName !!}{{ $loop->last ? '' : ', ' }}
                @endforeach
            </div>
        @endif
    @endif

    <div class="text-center">
        @if ($shop->has_image)
            <img src="{{ $shop->shopImageUrl }}" style="max-width:100%" alt="{{ $shop->name }}" />
        @endif
        <p>{!! $shop->parsed_description !!}</p>
    </div>

    @foreach ($stocks as $type => $stock)
        @if (count($stock))
            <h3>
                {{ $type . (substr($type, -1) == 's' ? '' : 's') }}
            </h3>
        @endif
        @if (Settings::get('shop_type'))
            @include('shops._tab', ['items' => $stock, 'shop' => $shop])
        @else
            @foreach ($stock as $categoryId => $categoryItems)
                @php
                    $visible = '';
                    // check if method exists
                    if (method_exists($categoryItems->first(), 'is_visible') && !$categories[$categoryId]->is_visible) {
                        $visible = '<i class="fas fa-eye-slash mr-1"></i>';
                    }
                @endphp
                <div class="card mb-3 inventory-category">
                    <h5 class="card-header inventory-header">
                        {!! isset($categories[$categoryId]) ? '<a href="' . $categories[$categoryId]->searchUrl . '">' . $visible . $categories[$categoryId]->name . '</a>' : 'Miscellaneous' !!}
                    </h5>
                    <div class="card-body inventory-body">
                        @foreach ($categoryItems->chunk(4) as $chunk)
                            <div class="row mb-3">
                                @foreach ($chunk as $item)
                                    <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $item->pivot->id }}">
                                        <div class="mb-1">
                                            <a href="#" class="inventory-stack"><img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" /></a>
                                        </div>
                                        <div>
                                            <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $item->name }}</strong></a>
                                            <div><strong>Cost: </strong> {!! $currencies[$item->pivot->currency_id]->display((int) $item->pivot->cost) !!}</div>
                                            @if ($item->pivot->is_limited_stock)
                                                <div>Stock: {{ $item->pivot->quantity }}</div>
                                            @endif
                                            @if ($item->pivot->purchase_limit)
                                                <div class="text-danger">Max {{ $item->pivot->purchase_limit }} @if ($item->pivot->purchase_limit_timeframe !== 'lifetime')
                                                        {{ $item->pivot->purchase_limit_timeframe }}
                                                    @endif per user</div>
                                            @endif
                                            @if ($item->pivot->disallow_transfer)
                                                <div class="text-danger">Cannot be transferred after purchase</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.inventory-item').on('click', function(e) {
                e.preventDefault();

                loadModal("{{ url('shops/' . $shop->id) }}/" + $(this).data('id'), 'Purchase Item');
            });
        });
    </script>
@endsection
