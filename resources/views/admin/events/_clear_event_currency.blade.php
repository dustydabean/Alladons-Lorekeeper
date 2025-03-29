@if($currency)
    {!! Form::open(['url' => 'admin/event-settings/clear']) !!}

    <p>You are about to clear the currency <strong>{{ $currency->name }}</strong>. This is not reversible. The currency itself will not be deleted, but it will be removed from all users. If there is currently a global total being tracked, it will also be set to 0. All teams' scores, if any exist, will also be set to 0.</p>
    <p>Are you sure you want to clear <strong>{{ $currency->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Clear Event Currency', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid currency selected.
@endif
