<div class="card mb-3">
    <div class="card-header">
        <h2 class="mb-0"><a href="{{$listing->url}} ">Listing (#{{ $listing->id }})</a> : Posted by {!! $listing->user->displayName !!}</h2>
    </div>
    <div class="card-body">
        @if(isset($trade->terms_link) && $trade->terms_link)
            <div class="row">
                <div class="col-md-2 col-4"><h5>Proof of Terms</h5></div>
                <div class="col-md-10 col-8"><a href="{{ $trade->terms_link }}">{{ $trade->terms_link }}</a></div>
            </div>
        @endif
        @if($listing->comments)
            <div class="mb-2">{!! nl2br(htmlentities($listing->comments)) !!}</div>
        @endif
            <p><strong>Contact Via:</strong> {!! nl2br(htmlentities($listing->contact)) !!}
            <hr />
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-heading">
                    Seeking:
                </h3>
                @include('home.trades.listings._seeking_summary', ['user' => $listing->user, 'data' => isset($listing->data['seeking']) ? parseAssetData($listing->data['seeking']) : null, 'listing' => $listing, 'etc' => isset($listing->data['seeking_etc']) ? $listing->data['seeking_etc'] : null])
            </div>
            <div class="col-md-6">
                <h3 class="card-heading">
                    Offering:
                </h3>
                @include('home.trades.listings._offer_summary', ['user' => $listing->user, 'data' => isset($listing->data['offering']) ? parseAssetData($listing->data['offering']) : null, 'listing' => $listing, 'etc' => isset($listing->data['offering_etc']) ? $listing->data['offering_etc'] : null])
            </div>
        </div>
        <hr />
        <div class="text-right">
            <a href="{{ $listing->url }}" class="btn btn-outline-primary">View Details</a>
        </div>
    </div>
</div>