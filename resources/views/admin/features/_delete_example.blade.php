@if($example)
    {!! Form::open(['url' => 'admin/data/traits/examples/delete/'.$example->id]) !!}

    <p>You are about to delete this example for the trait <strong>{{ $feature->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete it?</p>

    <img src="{{ $example->imageUrl }}" style="max-height:20em;" /></a>

    <div class="text-right">
        {!! Form::submit('Delete Example', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid example selected.
@endif
