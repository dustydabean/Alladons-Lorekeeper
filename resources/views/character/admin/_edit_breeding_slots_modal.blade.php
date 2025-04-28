{!! Form::open(['url' => 'admin/character/breeding-slot/' . $slot->id]) !!}

<div class="form-group row mb-2">
    <div class="col-12">
        {!! Form::label('user_id', 'Slot User') !!}
    </div>
    <div class="col">
        {!! Form::select('user_id', $users, $slot->user_id ?? null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select a User']) !!}
    </div>
    <div class="col">
        {!! Form::text('user_url', $slot->user_url ?? null, ['class' => 'form-control mr-2', 'placeholder' => 'Slot User URL']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('offspring_id', 'Slot Offspring') !!}
    {!! Form::select('offspring_id', $characterOptions, $slot->offspring_id ?? null, ['class' => 'form-control mr-2 selectize', 'placeholder' => 'Select an Offspring']) !!}
</div>

<div class="text-right">
    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
</script>
