<h3>You must collect...</h3>
<div class="mt-3 square-grid">
@foreach($collection->ingredients as $ingredient)
    <div class="square-column text-center">
        @switch($ingredient->ingredient_type)
            @case('Item')
                @php 
                    $user = Auth::user();
                    $userOwned = \App\Models\User\UserItem::where('user_id', $user->id)->where('item_id', $ingredient->ingredient->id)->where('count', '>', 0)->get();
                @endphp
                @if($userOwned->count() || Auth::user()->hasCollection($collection->id))
                    <div class="img-thumbnail" style="border: 1px solid grey;"><img src="{{ $ingredient->ingredient->image_url }}" /></div>
                @elseif(!Auth::user()->hasCollection($collection->id))
                    <div class="img-thumbnail"><img class="greyscale" src="{{ $ingredient->ingredient->image_url }}" /></div>
                @endif
        @endswitch
        <div class="text-center">{!! $ingredient->ingredient->displayName !!}</div>
    </div> 
@endforeach
</div>

<div class="mt-3">
    @if(Auth::check() && Auth::user()->hasCollection($collection->id))
        <div class="alert alert-success text-center">
            You have already completed this collection! 
        </div>
    @elseif($activity->service->checkCollection(Auth::user(), $collection))
    {!! Form::open(['url' => 'activities/' . $activity->id . '/act']) !!}
        <div class="text-right">
            {!! Form::submit('Complete!', ['class' => 'btn btn-success']) !!}
        </div>
    {!! Form::close() !!}
    @else
    <div class="alert alert-warning text-center">
        You don't have everything for this collection yet!
    </div>
    @endif
</div>