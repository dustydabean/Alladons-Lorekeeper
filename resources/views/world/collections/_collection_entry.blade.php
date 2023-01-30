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

                    @if(isset($collection->category) && $collection->category)
                <div class="col-md">
                    <p><strong>Category:</strong> <a href="{{  $collection->category->searchUrl }}">{!! $collection->category->name !!}</a></p>
                </div>
            @endif
                    <div class="world-entry-text">
                        {!! $collection->description !!}
                    </div>
                            <h5>Collection Requirements</h5>
                                <div class="alert alert-secondary">
                                    @include('home.collection._collection_ingredient_entry', ['ingredient' => $collection->ingredient])

                                    @if($collection->parent_id && Auth::check() && !Auth::user()->hasCollection($collection->id))
                                        
                                            <h4> Prerequisite </h4>
                                                @php 
                                                    if(Auth::check()) $completed = DB::table('user_collections')->where('user_id', Auth::user()->id)->where('collection_id', $collection->parent_id)->count();    
                                                @endphp
                                                    @if(!Auth::check() || !$completed)
                                                        <div class="alert alert-danger">This collection requires you to have completed {!! $collection->parent->displayName !!} before you can complete it.</div>
                                                    @else
                                                        <div class="alert alert-success">You've completed {!! $collection->parent->displayName !!} and can complete this collection!</div>
                                                    @endif
                                    @elseif(!Auth::check() && $collection->parent_id)  
                                    <div class="alert alert-danger">This collection requires you to have completed {!! $collection->parent->displayName !!} before you can complete it.</div>
                                    @endif
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
                        
                        @if(count($collection->children))
                            @if(Auth::check() && !Auth::user()->hasCollection($collection->id))
                                <h4 class="mt-2">Unlocks</h4>
                                    This collection unlocks the following collections:
                                <br>
                                <div class="alert alert-secondary">
                                    @foreach($collection->children as $children)
                                        {!! $children->displayname !!}
                                    @endforeach
                                    
                                </div>
                            @else
                            <h4 class="mt-2">Unlocks</h4>
                                    This collection unlocks the following collections:
                                <br>
                                <div class="alert alert-secondary">
                                    @foreach($collection->children as $children)
                                        {!! $children->displayname !!}
                                    @endforeach
                                    
                                </div>
                                @endif
                        @endif
                    </div>
    </div>
</div>

