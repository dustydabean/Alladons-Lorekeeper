@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}
@endsection

@section('meta-img')
    {{ $character->image->content_warnings ? asset('images/content-warning.png') : $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url]) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    @if ($character->images()->where('is_valid', 1)->where('transformation_id', '>', 0)->whereNotNull('transformation_id')->exists())
        <div class="card-header mb-2">
            <ul class="nav nav-tabs card-header-tabs">
                @foreach ($character->images()->where('is_valid', 1)->get() as $image)
                    <li class="nav-item">
                        <a class="nav-link form-data-button {{ $image->id == $character->image->id ? 'active' : '' }}" data-toggle="tab" role="tab" data-id="{{ $image->id }}">
                            {{ $image->transformation_id ? $image->transformation->name : 'Main' }}
                        </a>
                    </li>
                @endforeach
                <li>
                    <h3>{!! add_help('Click on the tabs to view the reference type of the alladon! You can also view other refs in the IMAGES sidebar tab.') !!}</h3>
                </li>
            </ul>
        </div>
    @endif

    {{-- Main Image --}}
    <div class="row mb-3" id="main-tab">
        <div @class([
            "col-md-6" => $character->image->longestSide === 'height' || $character->image->longestSide === 'square',
            "col-md-12" => $character->image->longestSide === 'width'
        ])>
            <div class="text-center">
                <a href="{{ $character->image->canViewFull(Auth::user() ?? null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                    data-lightbox="entry" data-title="{{ $character->fullName }}">
                    <img src="{{ $character->image->canViewFull(Auth::user() ?? null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                        class="image {{ $character->image->showContentWarnings(Auth::user() ?? null) ? 'content-warning' : '' }}" alt="{{ $character->fullName }}" />
                </a>
            </div>
            @if ($character->image->canViewFull(Auth::user() ?? null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)))
            <div class="text-center" style="padding-top: 8px">You are viewing the full-size image. <a href="{{ $character->image->imageUrl }}">View watermarked image</a>?</div>
            @endif
        </div>
        @include('character._image_info', ['image' => $character->image])
    </div>

    {{-- Info --}}
    @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="card">
        <div class="card-header">
            <a class="h5" href="#adminTab" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="adminTab">
                Admin Panel
            </a>
        </div>
        <div class="collapse" id="adminTab">
            <div class="card-body">
                {!! Form::open(['url' => $character->is_myo_slot ? 'admin/myo/' . $character->id . '/settings' : 'admin/character/' . $character->slug . '/settings']) !!}
                <div class="form-group">
                    {!! Form::checkbox('is_visible', 1, $character->is_visible, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Turn this off to hide the character. Only mods with the Manage Masterlist power (that\'s you!) can view it - the owner will also not be able to see the character\'s page.') !!}
                </div>
                <div class="text-right">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
                <hr />
                <div class="text-right">
                    <a href="#" class="btn btn-outline-danger btn-sm delete-character" data-slug="{{ $character->slug }}">Delete</a>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('scripts')
    @parent
    @include('widgets._datetimepicker_js', ['dtvalue' => $character->transferrable_at])
    @include('character._image_js', ['character' => $character])
    @include('character._transformation_js')
@endsection
