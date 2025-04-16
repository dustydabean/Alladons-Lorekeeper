<div class="col p-1">
    <div class="border-bottom mb-1">
        <span class="font-weight-bold">
            {{ $parent }}
        </span>
        <br>
        <a href="{{ $character ? $character->url : '#' }}" data-toggle="tooltip" data-placement="top"
            title="{!! $character ? '<img src=\'' . $character->image->thumbnailUrl . '\' class=\'img-thumbnail\' alt=\'Thumbnail for ' . $character->fullName . '\' style=\'height: 150px; width: 150px;\'>' : '<i class=\'fas fa-question-circle\'></i>' !!}">
            {{ $character ? $character->fullName : 'Unknown' }}
        </a>
    </div>

    @if ($max_depth > 0)
        <div class="row no-gutters">
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
