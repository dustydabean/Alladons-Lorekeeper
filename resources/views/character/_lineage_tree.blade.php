<div class="col p-1">
    <div class="border-bottom mb-1">
        <span class="font-weight-bold {{ $max_depth == config('lorekeeper.lineage.lineage_depth') - 1 ? 'h4' : '' }}">
            {{ $parent }}
        </span>
        <br>
        @if ($max_depth == config('lorekeeper.lineage.lineage_depth') - 1)
            <a href="{{ $character ? $character->url : '#' }}" class="lineage-popover btn btn-sm btn-primary{{ $max_depth == config('lorekeeper.lineage.lineage_depth') - 1 ? ' h4' : '' }}" data-container="body" data-toggle="popover" data-content="{{ $character ? '<img src="' . $character->image->thumbnailUrl . '" class=\'img-thumbnail\' alt=\'Thumbnail for ' . $character->fullName . '\' style=\'width: 150px;\'>' : '' }}">
                {!! $character ? $character->fullName : 'Unknown' !!}
            </a>
        @else
            <a href="{{ $character ? $character->url : '#' }}" class="lineage-popover btn btn-sm btn-primary" data-container="body" data-toggle="popover" data-content="{{ $character ? '<img src="' . $character->image->thumbnailUrl . '" class=\'img-thumbnail\' alt=\'Thumbnail for ' . $character->fullName . '\' style=\'width: 150px;\'>' : '' }}">
                {!! $character ? $character->fullName : 'Unknown' !!}
            </a>
        @endif
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
