<div class="row world-entry">
    @if ($transformation->transformationImageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $transformation->transformationImageUrl }}" data-lightbox="entry" data-title="{{ $transformation->name }}"><img src="{{ $transformation->transformationImageUrl }}" class="world-entry-image"
                    alt="{{ $transformation->name }}" /></a></div>
    @endif
    <div class="{{ $transformation->transformationImageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>{!! $transformation->displayName !!} <a href="{{ $transformation->searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a></h3>
        <div class="world-entry-text">
            {!! $transformation->parsed_description !!}
        </div>
    </div>
</div>
