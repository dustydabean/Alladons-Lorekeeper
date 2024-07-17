<div class="card mb-3" data-id="{{ $collection->id }}" data-name="{{ $collection->name }}">
    <div class="card-header">
        <h2 class="mb-0">{{ $collection->name }}</h2>
    </div>
    <div class="card-body text-center">
        @if(isset($collection->image_url))
            <div class="text-center mb-3">
                <img src="{{ $collection->imageUrl }}" class="collection-image">
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
                <h5>Items</h5>
                @for($i = 0; $i < count($collection->items) && $i < 3; ++$i)
                    <?php $item = $collection->items[$i]?>
                    <div class="alert alert-secondary">
                        @include('home.crafting._collection_item_entry', ['item' => $item])
                    </div>
                @endfor
                @if(count($collection->items) > 3)
                    <i class="fas fa-ellipsis-h mb-3"></i>
                @endif
            </div>
            <div class="col-md-6">
                <h5>Rewards</h5>
                <?php $counter = 0; ?>
                @foreach($collection->reward_items as $type)
                    @foreach($type as $item)
                        @if($counter > 3) @break @endif
                        <?php ++$counter; ?>
                        <div class="alert alert-secondary">
                            @include('home.crafting._collection_reward_entry', ['reward' => $item])
                        </div>
                    @endforeach
                @endforeach
                @if($counter > 3)
                    <i class="fas fa-ellipsis-h mb-3"></i>
                @endif
            </div>
        </div>
        <a class="btn btn-primary btn-block btn-craft" href="">Craft</a>
    </div>
</div>