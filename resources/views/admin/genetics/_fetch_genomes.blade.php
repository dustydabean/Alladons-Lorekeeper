@if($genomes)
    @foreach ($genomes as $genome)
        <li class="list-group-item text-center">
            @if (isset($preview) && $preview == true)
                @foreach ($genome as $gene){!! $gene !!}@endforeach
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
