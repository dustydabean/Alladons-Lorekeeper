<div class="col-auto p-1">
    <div class="border-bottom mb-1">
        <span class="font-weight-bold">
            {{ $parent }}
        </span>
        <br>
        <a href="{{ $character ? $character->url : '#' }}" class="lineage-popover m-1 btn btn-sm btn-primary" data-container="body" data-toggle="popover" data-content="{{ $character ? '<img src="' . $character->image->thumbnailUrl . '" class=\'img-thumbnail\' alt=\'Thumbnail for ' . $character->fullName . '\' style=\'width: 100px;\'>' : '' }}" style="white-space: normal;">
            {!! $character ? $character->number : 'Unknown' !!}
        </a>
    </div>

    @if ($max_depth > 0)
        <div class="px-0 row no-gutters flex-nowrap">
            @if (!empty($character?->lineage?->parent_1))
                @include('character._tab_lineage_col', [
                    'character' => $character?->lineage?->parent_1,
                    'max_depth' => config('lorekeeper.lineage.tab_lineage_depth') - 1,
                    'parent' => $character?->lineage?->parent_1?->parentType ?? 'Parent',
                ])
            @endif

            @if (!empty($character?->lineage?->parent_2))
                @include('character._tab_lineage_col', [
                    'character' => $character?->lineage?->parent_2,
                    'max_depth' => config('lorekeeper.lineage.tab_lineage_depth') - 1,
                    'parent' => $character?->lineage?->parent_2?->parentType ?? 'Parent',
                ])
            @endif
        </div>
    @endif
</div>
