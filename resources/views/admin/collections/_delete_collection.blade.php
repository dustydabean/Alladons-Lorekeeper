@if($collection)
    {!! Form::open(['url' => 'admin/data/collections/delete/'.$collection->id]) !!}

    <p>You are about to delete the collection <strong>{{ $collection->name }}</strong>. This is not reversible. If this collection exists in at least one user's possession, you will not be able to delete this collection.</p>
    <p>Are you sure you want to delete <strong>{{ $collection->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Collection', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid collection selected.
@endif