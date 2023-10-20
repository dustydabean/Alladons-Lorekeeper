@if ($drop)
    {!! Form::open(['url' => 'admin/data/pets/drops/edit/' . $pet->id . '/variants/delete/' . $variant->id]) !!}

    <p>You are about to delete the drop for <strong>{{ $variant->variant_name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $variant->variant_name }}</strong> drops?</p>

    <div class="text-right">
        {!! Form::submit('Delete Variant Drop', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid variant drop selected.
@endif
