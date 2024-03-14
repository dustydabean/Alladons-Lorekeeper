@if($loci)
    {!! Form::open(['url' => 'admin/genetics/delete/'.$loci->id]) !!}

    <p>You are about to delete the gene group <strong>{{ $loci->name }}</strong>. This is not reversible. All alleles in this gene group will be deleted. <strong>All characters with those genes will lose them.</strong></p>
    <p>Are you sure you want to delete <strong>{{ $loci->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Gene Group', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid category selected.
@endif
