@if($genomes)
    @foreach ($genomes as $genome)
        <li class="list-group-item text-center">
            @if (isset($preview) && $preview == true)
                @foreach ($genome as $key => $child)
                    @if ($key > 0 && count($genome)-1 != $key)<span class="text-monospace mx-2">//</span>@endif
                    @foreach ($child as $gene){!! $gene !!}@endforeach
                @endforeach
            @else
                @include('character._genes', ['genome' => $genome, 'buttons' => false])
            @endif
        </li>
    @endforeach
@else
    <li class="list-group-item text-center">
        You called out to the genomes! . . . but no one came.
    </li>
@endif
