<div class="row world-entry">
        @if ($feature->has_image)
        <div class="col-md-3 world-entry-image">
            <a href="{{ $feature->imageUrl }}" data-lightbox="entry" data-title="{{ $feature->name }}"><img src="{{ $feature->imageUrl }}" class="world-entry-image" alt="{{ $feature->name }}" style="max-height:30em;" /></a>
            @if ($feature->exampleImages->count() == 1)
                <div class="text-center">
                    <hr>
                    <h5>Example</h5>
                    <a href="{{ $feature->singleExample->imageUrl }}" data-lightbox="entry" data-title="{{ $feature->name . ' example' }}"><img src="{{ $feature->singleExample->imageUrl }}" class="world-entry-image"
                            alt="{{ $feature->name . ' example' }}" style="max-height:10em;" /></a>
                    @if ($feature->singleExample->summary)
                        <br>
                        {{ $feature->singleExample->summary }}
                    @endif
                </div>
            @elseif($feature->exampleImages->count() > 1)
                <div class="text-center">
                    <hr>
                    <h5>Examples</h5>
                    <div class="row justify-content-center">
                        <div id="example-carousel-{{ $feature->id }}" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                @foreach ($feature->exampleImages as $example)
                                    <li data-target="#example-carousel-{{ $feature->id }}" data-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                                @endforeach
                            </ol>
                            <div class="carousel-inner">
                                @foreach ($feature->exampleImages as $example)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <a href="{{ $example->imageUrl }}" data-lightbox="entry" data-title="{{ $feature->name . ' example' }}"><img class="d-block w-100" style="max-width: 14em;" src="{{ $example->imageUrl }}"></a>
                                        @if ($example->summary)
                                            {{ $example->summary }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#example-carousel-{{ $feature->id }}" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#example-carousel-{{ $feature->id }}" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    @endif
    <div class="{{ $feature->has_image ? 'col-md-9' : 'col-12' }}">
        <x-admin-edit title="Trait" :object="$feature" />
        <h3>
            @if (!$feature->is_visible)
                <i class="fas fa-eye-slash mr-1"></i>
            @endif
            {!! $feature->displayName !!}
            <a href="{{ $feature->searchUrl }}" class="world-entry-search text-muted">
                <i class="fas fa-search"></i>
            </a>
        </h3>
        @if ($feature->feature_category_id)
            <div>
                <strong>Category:</strong> {!! $feature->category->displayName !!}
            </div>
        @endif
        @if ($feature->mut_level)
            <div>
                <strong>Level:</strong> {!! $feature->level !!}
            </div>
        @endif
        <div>
            <strong>Status:</strong> {!! $feature->is_locked ? 'Locked' : 'Unlocked' !!}
        </div>
        @if ($feature->mut_type)
            <div>
                <strong>Type:</strong> {!! $feature->type !!}
            </div>
        @endif
        @if ($feature->species_id)
            <div>
                <strong>Species:</strong> {!! $feature->species->displayName !!}
                @if ($feature->subtype_id)
                    ({!! $feature->subtype->displayName !!} subtype)
                @endif
            </div>
        @endif
        <div class="world-entry-text parsed-text">
            {!! $feature->parsed_description !!}
        </div>
    </div>
</div>
