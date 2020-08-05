@extends('home.trades.listings.layout')

@section('trade-title') Listing (#{{ $listing->id }}) @endsection

@section('trade-content')
{!! breadcrumbs(['Trades' => 'trades/open', 'Listings' => 'trades/listings', 'Listing (#' . $listing->id . ')' => 'listing/'.$listing->id]) !!}

<h1>
    Trade Listing

    <span class="float-right badge badge-{{ $listing->isActive ? 'success' : 'secondary' }}">{{ $listing->isActive ? 'Active' : 'Expired' }}</span>
</h1>

<div class="mb-1">
    <div class="row">
        <div class="col-md-2 col-4"><h5>User</h5></div>
        <div class="col-md-10 col-8">{!! $listing->user->displayName !!}</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Created</h5></div>
        <div class="col-md-10 col-8">{!! format_date($listing->created_at) !!} ({{ $listing->created_at->diffForHumans() }})</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Last Updated</h5></div>
        <div class="col-md-10 col-8">{!! format_date($listing->updated_at) !!} ({{ $listing->updated_at->diffForHumans() }})</div>
    </div>
    <div>
        <div><h5>Comments</h5></div>
        <div class="card mb-3">
            <div class="card-body">
                @if($listing->comments)
                    {!! nl2br(htmlentities($listing->comments)) !!}
                @else 
                    No comment given.
                @endif
            </div>
        </div>
        <div><h5>Preferred Method(s) of Contact</h5></div>
        <div class="card mb-3">
            <div class="card-body">
                    {!! nl2br(htmlentities($listing->contact)) !!}
            </div>
        </div>
    </div>
</div>

<h2>Seeking & Offering</h2>
<div class="row">
    <div class="col-lg">
        <p>Seeking</p>
    </div>

    <div class="col-lg">
        @include('home.trades.listings._offer', ['user' => $listing->user, 'data' => $listing->data['offering'], 'trade' => $listing])
    </div>

</div>

@endsection
