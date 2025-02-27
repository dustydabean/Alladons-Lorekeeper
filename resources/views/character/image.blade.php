<div @class([
    "col-md-6" => $image->longestSide === 'height' || $image->longestSide === 'square',
    "col-md-12" => $image->longestSide === 'width'
])>
    <div class="text-center">
        <a href="{{ $image->canViewFull(Auth::user() ?? null) && file_exists(public_path($image->imageDirectory . '/' . $image->fullsizeFileName)) ? $image->fullsizeUrl : $image->imageUrl }}"
            data-lightbox="entry" data-title="{{ $character->fullName }}">
            <img src="{{ $image->canViewFull(Auth::user() ?? null) && file_exists(public_path($image->imageDirectory . '/' . $image->fullsizeFileName)) ? $image->fullsizeUrl : $image->imageUrl }}"
                class="image {{ $image->showContentWarnings(Auth::user() ?? null) ? 'content-warning' : '' }}" alt="{{ $character->fullName }}" />
        </a>
    </div>
    @if ($image->canViewFull(Auth::user() ?? null) && file_exists(public_path($image->imageDirectory . '/' . $image->fullsizeFileName)))
    <div class="text-center" style="padding-top: 8px">You are viewing the full-size image. <a href="{{ $image->imageUrl }}">View watermarked image</a>?</div>
    @endif
</div>
@include('character._image_info', ['image' => $image])