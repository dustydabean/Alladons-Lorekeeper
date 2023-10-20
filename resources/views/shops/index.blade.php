@extends('shops.layout')

@section('shops-title')
    Shop Index
@endsection

@section('shops-content')
    {!! breadcrumbs(['Shops' => 'shops']) !!}

    <h1>
        Shops
    </h1>

    <div class="row shops-row">
        @foreach ($shops as $shop)
            @if ($shop->is_staff)
                @if (auth::check() && auth::user()->isstaff)
                    @include('shops._shop')
                @endif
            @else
                @include('shops._shop')
            @endif
        @endforeach
    </div>
@endsection
