@if($devLogs)
    {!! Form::open(['url' => 'admin/logs/delete/'.$devLogs->id]) !!}

    <p>You are about to delete the dev log post <strong>{{ $devLogs->title }}</strong>. This is not reversible. If you would like to preserve the content while preventing users from accessing the post, you can use the viewable setting instead to hide the post.</p>
    <p>Are you sure you want to delete <strong>{{ $devLogs->title }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Post', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid post selected.
@endif