@if ($pet)
    {!! Form::open(['url' => 'admin/data/pets/delete/' . $pet->id]) !!}

    <p>You are about to delete the companion <strong>{{ $pet->name }}</strong>. This is not reversible. If this companion exists in at least one user's possession, you will not be able to delete this companion.</p>
    <p>Are you sure you want to delete <strong>{{ $pet->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Companion', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid companion selected.
@endif
