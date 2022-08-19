<div class="row world-entry">
    <div class="col-12">
        <h3 class="mb-0">{!! $loci->name !!}</h3>
        @if ($loci->type == "gene")
            <strong>Type</strong>: Standard Allele<br>
            <strong>Alleles</strong>:
            @foreach ($loci->alleles as $allele)
                <div class="d-inline text-monospace px-1" title="{{ "Description here." }}">
                    {!! $allele->displayName !!}
                </div>
            @endforeach
        @else
            <strong>Type</strong>: {{ ucfirst($loci->type) }}<br>
            <strong>Range</strong>: 0-{{ $loci->length }}
        @endif
        <div class="world-entry-text mt-2">
            {!! $loci->description !!}
        </div>
    </div>
</div>
