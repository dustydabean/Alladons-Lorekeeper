@if($activity)
    {!! Form::open(['url' => 'admin/data/activities/delete/'.$activity->id]) !!}

    <p>You are about to delete the activity <strong>{{ $activity->name }}</strong>. This is not reversible. If you would like to hide the activity from users, you can set it as inactive from the activity settings page.</p>
    <p>Are you sure you want to delete <strong>{{ $activity->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Activity', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid activity selected.
@endif