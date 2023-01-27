<div class="row">
    <div class="col-lg-6 col-lg-10 mx-auto">

    {!! $collection->usersincomplete !!}
        
            <div class="card-body">
                @if($collection->imageUrl)
                    <div class="world-entry-image text-center mb-2"><a href="{{ $collection->imageUrl }}" data-lightbox="entry" data-title="{{ $collection->name }}"><img src="{{ $collection->imageUrl }}" class="world-entry-image mw-100" style="max-height:300px;" /></a></div>
                @endif
                <div>
                    <h1>
                        {!! $collection->displayName !!} @if(Auth::check() && Auth::user()->hasCollection($collection->id))
                        <i class="fas fa-check" data-toggle="tooltip" title="You've completed this collection."></i>@endif
                    </h1>
                    <div class="world-entry-text">
                        {!! $collection->description !!}
                    </div>
                            <h5>Collection Requirements</h5>
                                <div class="alert alert-secondary">
                                    @include('home.collection._collection_ingredient_entry', ['ingredient' => $collection->ingredient])
                                </div>
                        </div>
                            <h5>Rewards</h5>
                            @foreach($collection->reward_items as $type)
                                @foreach($type as $item)
                                    <div class="alert alert-secondary">
                                        @include('home.collection._collection_reward_entry', ['reward' => $item])
                                    </div>
                                @endforeach
                            @endforeach
                        
                    </div>
    </div>
</div>