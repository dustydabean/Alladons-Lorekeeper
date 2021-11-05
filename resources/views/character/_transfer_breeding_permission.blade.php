@if($breedingPermission)
    {!! Form::open(['url' => 'character/'.$character->slug.'/breeding-permissions/'.$breedingPermission->id.'/transfer']) !!}

    <p>
        This will transfer this breeding permission to the selected user.
        @if(Auth::user()->id != $breedingPermission->recipient_id)
            It will also notify the original recipient ({!! $breedingPermission->recipient->displayName !!}).
        @endif
    </p>

    <div class="form-group">
        {!! Form::label('recipient_id', 'Recipient') !!}
        {!! Form::select('recipient_id', $userOptions, null, ['class' => 'form-control', 'placeholder' => 'Select a Recipient', 'id' => 'recipientField']) !!}
    </div>

    <div class="form-group text-right">
        {!! Form::submit('Transfer', ['class' => 'btn btn-success']) !!}
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function() {
            $('#recipientField').selectize();
        });
    </script>
@else
    <p>Invalid breeding permission selected.</p>
@endif
