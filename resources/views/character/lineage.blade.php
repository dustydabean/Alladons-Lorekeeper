@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
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

    <div class="container justify-content-center">
        <div class="text-center">
            <a href="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                data-lightbox="entry" data-title="{{ $character->fullName }}">
                <img src="{{ $character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)) ? $character->image->fullsizeUrl : $character->image->imageUrl }}"
                    class="image mb-2" style="max-height: 40vh !important;" alt="{{ $character->fullName }}" />
            </a>
        </div>
        @if ($character->image->canViewFull(Auth::check() ? Auth::user() : null) && file_exists(public_path($character->image->imageDirectory . '/' . $character->image->fullsizeFileName)))
            <div class="text-right">You are viewing the full-size image. <a href="{{ $character->image->imageUrl }}">View watermarked image</a>?</div>
        @endif
    </div>

    @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
        <div class="my-3">
            <a href="#" class="btn btn-outline-info btn-sm edit-lineage" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
        </div>
    @endif

    {{-- collapse for descendants --}}
    @if ($character->getLineageBlacklistLevel() < 1)
        <div class="card mb-3">
            <div class="card-header" data-toggle="collapse" data-target="#descendants" aria-expanded="false" aria-controls="descendants">
                <h2 class="h3">
                    <i class="fas fa-chevron-down"></i> Show Descendants
                </h2>
            </div>
            <div class="collapse" id="descendants">
                <div class="card card-body">
                    <div class="row">
                        @include('character._lineage_children', [
                            'character' => $character,
                            'max_depth' => config('lorekeeper.lineage.descendant_depth') - 1,
                            'title' => 'Children',
                        ])
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- collapse for ancestors --}}
    {{-- ancestors always exist if this page is accessible --}}
    <div class="card mb-3">
        <div class="card-header" data-toggle="collapse" data-target="#ancestors" aria-expanded="false" aria-controls="ancestors">
            <h2 class="h3">
                <i class="fas fa-chevron-down"></i> Show Ancestors
            </h2>
        </div>
        <div class="collapse" id="ancestors">
            <div class="card card-body">
                <div class="row text-center">
                    @include('character._lineage_tree', [
                        'character' => $character->lineage?->parent_1,
                        'max_depth' => config('lorekeeper.lineage.lineage_depth') - 1,
                        'parent' => $character->lineage?->parent_1->parentType ?? 'Parent',
                    ])
                    @include('character._lineage_tree', [
                        'character' => $character->lineage?->parent_2,
                        'max_depth' => config('lorekeeper.lineage.lineage_depth') - 1,
                        'parent' => $character->lineage?->parent_2->parentType ?? 'Parent',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.edit-lineage').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/lineage", 'Edit Character Lineage');
        });
    </script>
@endsection
