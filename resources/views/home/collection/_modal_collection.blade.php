@if(!$collection)
    <div class="text-center">Invalid collection selected.</div>
@else
    @if($collection->imageUrl)
        <div class="text-center">
            <div class="mb-3"><img class="collection-image" src="{{ $collection->imageUrl }}"/></div>
        </div>
    @endif
    <h3>Collection Details <a class="small inventory-collapse-toggle collapse-toggle" href="#collectionDetails" data-toggle="collapse">Show</a></h3>
    <hr>
    <div class="collapse show" id="collectionDetails">
        <div class="row">
            
            <div class="col-md-6">
                <h5>Collection Items</h5>
                @foreach($collection->ingredients as $ingredient)
                    <div class="alert alert-secondary">
                        @include('home.collection._collection_ingredient_entry', ['ingredient' => $ingredient])
                    </div>
                @endforeach
            </div>
            <div class="col-md-6">
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
    @if($selected)
        {{-- Check if sufficient ingredients have been selected? --}}
        {!! Form::open(['url' => 'collection/complete/'.$collection->id]) !!}
            @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => $selected, 'page' => $page])
            <div class="text-right">
                {!! Form::submit('Complete', ['class' => 'btn btn-primary']) !!}
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