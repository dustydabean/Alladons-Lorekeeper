<div class="container-fluid px-0 text-center {{ isset($tab) && $tab ? 'mb-3' : '' }}">
    {{-- recursive based on config --}}
    @if ($character->children && $character->children->count())
        <h5 class="text-center">
            {{ $title }}
        </h5>
        @foreach ($character->children->chunk(5) as $chunk)
            <div class="row justify-content-center">
                @foreach ($chunk as $child)
                    @if ($child->character)
                        <div class="col-sm-2 text-center">
                            <div>
                                <a href="{{ $child->character->url }}">
                                    <img src="{{ $child->character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $child->character->fullName }}" width="100" height="100"/>
                                </a>
                            </div>
                            <div class="mt-1">
                                <a href="{{ $child->character->url }}" class="h5 mb-0">
                                    @if (!$child->character->is_visible)
                                        <i class="fas fa-eye-slash"></i>
                                    @endif 
                                    {{ Illuminate\Support\Str::limit($child->character->fullName, 20, $end = '...') }}
                                </a>
                            </div>
                            @if ($child->character->children->count() && $max_depth > 0)
                                <hr>
                                <div class="row">
                                    @include('character._lineage_children', [
                                        'character' => $child->character,
                                        'max_depth' => $max_depth - 1,
                                        'title' => $max_depth == config('lorekeeper.lineage.descendant_depth') - 1 ? 'Grandchildren' : str_repeat('Great-', config('lorekeeper.lineage.descendant_depth') - $max_depth - 1) . 'Grandchildren',
                                    ])
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
        {!! isset($tab) && $tab ? '<hr />' : '' !!}
    @else
        <div class="alert alert-info text-center">No Descendants</div>
    @endif
</div>
