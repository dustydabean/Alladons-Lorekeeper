<div class="row world-entry">
    @if ($pet->imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $pet->imageUrl }}" data-lightbox="entry" data-title="{{ $pet->name }}">
                <img src="{{ $pet->imageUrl }}" class="world-entry-image" alt="{{ $pet->name }}" />
            </a></div>
    @endif
    <div class="{{ $pet->imageUrl ? 'col-md-9' : 'col-12' }}">
        <h2 class="h3">{!! $pet->displayName !!} @if ($pet->category)
                <i class="h4"> ({!! $pet->category->displayName !!})</i>
            @endif
        </h2>
        <div class="world-entry-text">
            {!! $pet->description !!}
            <div class="container mt-2">
                @if ($pet->variants->count())
                    <hr />
                    <h2 class="h4 pl-2">Variants</h2>
                    @foreach ($pet->variants->chunk(4) as $chunk)
                        <div class="row">
                            @foreach ($chunk as $variant)
                                <div class="col-md text-center">
                                    @if ($variant->has_image)
                                        <a href="{{ $variant->imageUrl }}" data-lightbox="entry" data-title="{{ $variant->variant_name }}">
                                            <img src="{{ $variant->imageUrl }}" class="img-fluid" style="max-height: 10em;" alt="{{ $variant->variant_name }}" data-toggle="tooltip" data-title="{{ $variant->variant_name }}" style="max-height:200px" />
                                        </a>
                                    @else
                                        {{ $variant->name }}
                                    @endif
                                    <p>{{ $variant->description }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
                @if ($pet->evolutions->count())
                    <hr />
                    <h2 class="h4 pl-2">Evolutions</h2>
                    @foreach ($pet->evolutions->sortBy('evolution_stage')->chunk(4) as $chunk)
                        <div class="row">
                            @foreach ($chunk as $evolution)
                                <div class="col">
                                    <div class="card h-100 mb-3 border-0">
                                        <div class="card-body text-center">
                                            <a href="{{ $evolution->imageUrl }}" data-lightbox="entry" data-title="{{ $evolution->evolution_name }}">
                                                <img src="{{ $evolution->imageUrl }}" class="img-fluid" style="max-height: 10em;" alt="{{ $evolution->evolution_name }}" data-toggle="tooltip" data-title="{{ $evolution->evolution_name }}"
                                                    style="max-height:200px" />
                                            </a>
                                            <div class="h5 my-2">
                                                {{ $evolution->evolution_name }} (Stage {{ $evolution->evolution_stage }})
                                                @if (!$loop->last)
                                                    <i class="fas fa-arrow-right fa-lg mt-2"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
            @if ($pet->hasDrops)
                <div class="alert alert-info mt-4">
                    This pet has drops! <a href="{{ $pet->idUrl }}">Click here to view them</a>.
                </div>
            @endif
        </div>
    </div>
</div>
