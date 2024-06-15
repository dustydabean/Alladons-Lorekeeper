@if(!$raffle->id)
    <p>
        Enter basic information about this raffle. Tickets can be added after the raffle is created.
    </p>
@endif
{!! Form::open(['url' => 'admin/raffles/edit/raffle/'.($raffle->id ? : '')]) !!}
    <div class="form-group">
        {!! Form::label('name', 'Raffle Name') !!} {!! add_help('This is the name of the raffle. Naming it something after what is being raffled is suggested (does not have to be unique).') !!}
        {!! Form::text('name', $raffle->name, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('winner_count', 'Number of Winners to Draw') !!}
        {!! Form::text('winner_count', $raffle->winner_count, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('group_id', 'Raffle Group') !!} {!! add_help('Raffle groups must be created before you can select them here.') !!}
        {!! Form::select('group_id', $groups, $raffle->group_id, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('order', 'Raffle Order') !!} {!! add_help('Enter a number. If a group of raffles is rolled, raffles will be drawn in ascending order.') !!}
        {!! Form::text('order', $raffle->order ? : 0, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        <label class="control-label">
            {!! Form::checkbox('is_active', 1, $raffle->is_active, ['class' => 'form-check-input mr-2', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_displayed', 'Active (visible to users)', ['class' => 'form-check-label ml-3']) !!}
        </label>
    </div>

    @if($raffle->id && $raffle->is_active)
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('bump', 1, null, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('bump', 'Bump Raffle', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If toggled on, this will alert users that there is a new raffle. Best in conjunction with a clear notification of changes!') !!}
            </div>
        </div>
    @endif
    <div class="text-right">
        {!! Form::submit('Confirm', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}