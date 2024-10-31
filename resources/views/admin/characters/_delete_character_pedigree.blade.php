@if ($pedigree)
    {!! Form::open(['url' => 'admin/data/character-pedigrees/delete/' . $pedigree->id]) !!}

    <p>You are about to delete the pedigree tag <strong>{{ $pedigree->name }}</strong>. This is not reversible. If characters in this pedigree tag exist, you will not be able to delete this pedigree tag.</p>
    <p>Are you sure you want to delete <strong>{{ $pedigree->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Pedigree', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid pedigree tag selected.
@endif
