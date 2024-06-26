<div class="row">
    @foreach($activities as $activity)
        <div class="col-md-3 col-6 mb-3 text-center">
            <div class="shop-image">
                <a href="{{ $activity->url }}"><img class="img-fluid" src="{{ $activity->imageUrl }}" alt="{{ $activity->name }}" /></a>
            </div>
            <div class="shop-name mt-1">
                <a href="{{ $activity->url }}" class="h5 mb-0">{{ $activity->name }}</a>
            </div>
        </div>
    @endforeach
</div>