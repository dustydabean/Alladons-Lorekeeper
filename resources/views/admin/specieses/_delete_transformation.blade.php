@if ($transformation)
    {!! Form::open(['url' => 'admin/data/transformations/delete/' . $transformation->id]) !!}

    <p>You are about to delete the transformation <strong>{{ $transformation->name }}</strong>. This is not reversible. If traits and/or characters that have this transformation exist, you will not be able to delete this transformation.</p>
    <p>Are you sure you want to delete <strong>{{ $transformation->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Transformation', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid transformation selected.
@endif
