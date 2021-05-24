@if ($character->genomes->count())
    @foreach ($character->genomes as $genome)
        @include('character._genes', ['genome' => $genome])
    @endforeach
@else
    <div>No genes listed.</div>
@endif
