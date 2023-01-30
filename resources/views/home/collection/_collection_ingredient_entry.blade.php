

<div class="card-body tab-content">
    <div>
        <div class="row mb-3">
@foreach($collection->ingredients as $ingredient)
    @switch($ingredient->ingredient_type)
        @case('Item')
            <div class="col-sm-3 col-6 text-center inventory-item">
                <div class="mb-1">
                    @if(Auth::check())
                @php 
                $user = Auth::user();
                $userOwned = \App\Models\User\UserItem::where('user_id', $user->id)->where('item_id', $ingredient->ingredient->id)->where('count', '>', 0)->get();
                $userOwned->pluck('count')->sum();

                @endphp
                @endif
                
                @if(Auth::check())
                    @if($userOwned->count() || Auth::user()->hasCollection($collection->id)) <img src="{{ $ingredient->ingredient->image_url }}" />
                    @elseif(!Auth::user()->hasCollection($collection->id))   <img class="collectionnotunlocked" src="{{ $ingredient->ingredient->image_url }}" /> @endif
                @else
                <img src="{{ $ingredient->ingredient->image_url }}" />
                @endif
                </div>
                    <div>{!! $ingredient->ingredient->displayName !!} 
                        @if(Auth::check() && !Auth::user()->hasCollection($collection->id))
                            @if($userOwned->count())
                            <i class="fas fa-check"></i>
                            @else 
                            ({{ $ingredient->quantity }})
                            @endif
                        @elseif(!Auth::check())
                        ({{ $ingredient->quantity }})
                        @else
                         
                        @endif
                    </div>
            </div>
    @endswitch
@endforeach
        </div>
    </div>
</div>

