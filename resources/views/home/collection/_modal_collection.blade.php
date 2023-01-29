@if(!$collection)
    <div class="text-center">Invalid collection selected.</div>
@elseif(Auth::check() && Auth::user()->hasCollection($collection->id))   
    @if($collection->imageUrl)
        <div class="text-center">
            <div class="mb-3"><img class="collection-image" src="{{ $collection->imageUrl }}"/></div>
        </div>
    @endif
    <h3>Collection Details <a class="small inventory-collapse-toggle collapse-toggle" href="#collectionDetails" data-toggle="collapse">Show</a></h3>
    <hr>
    <div class="collapse show" id="collectionDetails">

                <h5>Collection Items</h5>
                    <div class="alert alert-secondary">
                        @include('home.collection._collection_ingredient_entry', ['ingredient' => $collection->ingredient])
                    </div>
            </div>

            <div class="alert alert-success text-center">
        You have already completed this collection! 
        </div>
@else
    @if($collection->imageUrl)
        <div class="text-center">
            <div class="mb-3"><img class="collection-image" src="{{ $collection->imageUrl }}"/></div>
        </div>
    @endif
    <h3>Collection Details <a class="small inventory-collapse-toggle collapse-toggle" href="#collectionDetails" data-toggle="collapse">Show</a></h3>
    <hr>
    <div class="collapse show" id="collectionDetails">
        <div class="alert alert-success text-center">
        Completing a collection will not debit your items!
        </div>

                <h5>Collection Requirements</h5>
                    <div class="alert alert-secondary">
                        @include('home.collection._collection_ingredient_entry', ['ingredient' => $collection->ingredient])

                        @if($collection->parent_id)
                                        @if(Auth::check() && !Auth::user()->hasCollection($collection->id))
                                            <h4> Prerequisite </h4>
                                                @php 
                                                    if(Auth::check()) $completed = DB::table('user_collections')->where('user_id', Auth::user()->id)->where('collection_id', $collection->parent_id)->count();    
                                                @endphp
                                                    @if(!Auth::check() || !$completed)
                                                        <div class="alert alert-danger">This collection requires you to have completed {!! $collection->parent->displayName !!} before you can complete it.</div>
                                                    @else
                                                        <div class="alert alert-success">You've completed {!! $collection->parent->displayName !!} and can complete this collection!</div>
                                                    @endif
                                        @endif
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


        @if($selected)
            {{-- Check if sufficient ingredients have been selected? --}}
            {!! Form::open(['url' => 'collection/complete/'.$collection->id]) !!}
                @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => $selected, 'page' => $page])
                <div class="text-right">
                    {!! Form::submit('Complete!', ['class' => 'btn btn-success']) !!}
                </div>
            {!! Form::close() !!}
        @else
            <div class="alert alert-danger">You do not have all of the required collection items.</div>
        @endif
@endif

@include('widgets._inventory_select_js')
<script>
    $(document).keydown(function(e) {
    var code = e.keyCode || e.which;
    if(code == 13)
        return false;
    });
</script>