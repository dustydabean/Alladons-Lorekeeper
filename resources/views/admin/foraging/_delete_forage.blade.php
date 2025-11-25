@if($table)
    {!! Form::open(['url' => 'admin/data/forages/delete/'.$table->id]) !!}

    <p>You are about to delete the forage <strong>{{ $table->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $table->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Forage', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid forage selected.
@endif