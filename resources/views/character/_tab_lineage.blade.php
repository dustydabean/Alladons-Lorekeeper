<div class="container-fluid px-0 text-center" style="overflow-x: auto;">
    @if ($character->children->count() && config('lorekeeper.lineage.show_children_on_tab'))
        @include('character._lineage_children', [
            'character' => $character,
            'max_depth' => 0,
            'title' => 'Children',
            'tab' => true,
        ])
    @endif
    <h5>
        {{ $character->fullName }}'s Lineage
    </h5>
    {!! $character ? '<img src="' . $character->image->thumbnailUrl . '" class=\'img-thumbnail\' alt=\'Thumbnail for ' . $character->fullName . '\' / width="150" height="150">' : '' !!}
    <br>
    {!! $character ? $character->fullName : 'Unkown' !!}
    <div class="row no-gutters flex-nowrap">
        @include('character._tab_lineage_col', [
            'character' => $character?->lineage?->parent_1,
            'max_depth' => config('lorekeeper.lineage.tab_lineage_depth') - 1,
            'parent' => $character?->lineage?->parent_1?->parentType ?? 'Parent',
        ])
        @include('character._tab_lineage_col', [
            'character' => $character?->lineage?->parent_2,
            'max_depth' => config('lorekeeper.lineage.tab_lineage_depth') - 1,
            'parent' => $character?->lineage?->parent_2?->parentType ?? 'Parent',
        ])
    </div>
</div>

@if (Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-lineage" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif
