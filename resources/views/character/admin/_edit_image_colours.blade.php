{!! Form::open(['url' => 'admin/character/image/' . $image->id . '/colours', 'class' => 'form-horizontal']) !!}

{!! Form::hidden('edit', true) !!}

<div class="mt-2">
    @foreach (json_decode($image->colours, false) as $key => $colour)
        <div class="form-group">
            {!! Form::color('colours[' . $key . ']', $colour, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    @endforeach
</div>

{!! Form::submit('Update', ['class' => 'btn btn-primary']) !!}

{!! Form::close() !!}
