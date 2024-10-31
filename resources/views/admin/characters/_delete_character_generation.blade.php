@if ($generation)
    {!! Form::open(['url' => 'admin/data/character-generations/delete/' . $generation->id]) !!}

    <p>You are about to delete the generation <strong>{{ $generation->name }}</strong>. This is not reversible. If any characters have this generation, you will not be able to delete this generation.</p>
    <p>Are you sure you want to delete <strong>{{ $generation->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Generation', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid generation selected.
@endif
