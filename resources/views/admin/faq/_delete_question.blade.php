@if ($faq)
    {!! Form::open(['url' => 'admin/data/faq/delete/' . $faq->id]) !!}

    <p>
        You are about to delete the question
        <br>
        <strong>{{ $faq->question }}</strong>
        <br>
        This is not reversible.
    </p>
    <p>Are you sure you want to delete it?</p>

    <div class="text-right">
        {!! Form::submit('Delete Question', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid question selected.
@endif
