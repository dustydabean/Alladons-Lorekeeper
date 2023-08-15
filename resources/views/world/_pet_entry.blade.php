<div class="row world-entry">
    @if($pet->imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $pet->imageUrl }}" data-lightbox="entry" data-title="{{ $pet->name }}">
            <img src="{{ $pet->imageUrl }}" class="world-entry-image" alt="{{ $pet->name }}" />
        </a></div>
    @endif
    <div class="{{ $pet->imageUrl ? 'col-md-9' : 'col-12' }}">
        <h2 class="h3">{!! $pet->displayName !!}  @if($pet->category)<i class="h4"> ({!! $pet->category->displayName !!})</i>@endif</h2>
        <div class="world-entry-text">
            {!! $pet->description !!}
            <div class="container mt-2">
                @if($pet->variants->count())
                    <hr />
                    <h2 class="h4 pl-2">Variants</h2>
                    @foreach($pet->variants->chunk(4) as $chunk)
                        <div class="row">
                            @foreach($chunk as $variant)
                                <div class="col">
                                    @if($variant->has_image)
                                        <a href="{{ $variant->imageUrl }}" data-lightbox="entry" data-title="{{ $variant->variant_name }}">
                                            <img src="{{ $variant->imageUrl }}" class="img-fluid" alt="{{ $variant->variant_name }}" data-toggle="tooltip" data-title="{{ $variant->variant_name }}" style="max-height:200px" />
                                        </a>
                                    @else
                                        {{ $variant->name }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
            @if($pet->hasDrops)
                <div class="alert alert-info mt-4">
                    This pet has drops! <a href="{{ $pet->idUrl }}">Click here to view them</a>.
                </div>
            @endif
        </div>
    </div>
</div>
