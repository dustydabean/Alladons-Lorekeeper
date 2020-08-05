@extends('home.trades.listings.layout')

@section('trade-title') Listings @endsection

@section('trade-content')
{!! breadcrumbs(['Trades' => 'trades/open', 'Listings' => 'trades/listings']) !!}

<h1>
    Trade Listings
</h1>

<p>Here are all active trade listings. Listings are active for {{ $listingDuration }} days before they expire, after which they can only be viewed via their permalink. Note that listings only display what a user is seeking or offering based on the listing as entered, and do not directly interact with the trade system or update automatically-- users are responsible for updating their own listings as appropriate.</p>

<div class="text-right">
    <a href="{{ url('trades/listings/create') }}" class="btn btn-primary">New Trade Listing</a>
</div>

{!! $listings->render() !!}
@foreach($listings as $listing)
    {{ $listing->id }}
@endforeach
{!! $listings->render() !!}


@endsection
