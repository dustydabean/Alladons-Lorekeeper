@if($loci)
    {!! Form::open(['url' => 'admin/genetics/delete-allele/'.$loci->id]) !!}

    <p>Here, you can delete an allele from <strong>{{ $loci->name }}</strong> and replace all instances of it in character genomes with another allele from the same gene group.</p>

    <div class="row no-gutters">
        <div class="col-6 pr-2 form-group">
            {!! Form::label('target_allele', "Target") !!}
            {!! Form::select('target_allele', $alleles, null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-6 pl-2 form-group">
            {!! Form::label('replacement_allele', "Replacement") !!}
            {!! Form::select('replacement_allele', $alleles, null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit('Delete Allele', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid category selected.
@endif
