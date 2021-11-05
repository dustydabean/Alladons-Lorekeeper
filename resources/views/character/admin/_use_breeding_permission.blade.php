@if($breedingPermission)
    {!! Form::open(['url' => 'admin/character/'.$character->slug.'/breeding-permissions/'.$breedingPermission->id.'/use']) !!}

    <p>
        This will marked this breeding permission as being used. This is not reversible. Are you sure you want to mark this breeding permission as used?
    </p>

    <div class="form-group text-right">
        {!! Form::submit('Mark Used', ['class' => 'btn btn-success']) !!}
    </div>

    {!! Form::close() !!}
@else
    <p>Invalid breeding permission selected.</p>
@endif
