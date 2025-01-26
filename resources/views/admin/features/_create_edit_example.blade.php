{!! Form::open(['url' => $example->id ? 'admin/data/traits/examples/' . $feature->id . '/edit/' . $example->id : 'admin/data/traits/examples/' . $feature->id . '/create', 'files' => true]) !!}

<div class="row">
    <div class="col-md-6 form-group">
        {!! Form::label('Image (Required)') !!} {!! add_help('Image for the example.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
    </div>
    <div class="col-md-6 form-group">
        {!! Form::label('Example Summary (Optional)') !!} {!! add_help('This is a short blurb that shows up under the example image. HTML cannot be used here.') !!}
        {!! Form::text('summary', $example->summary, ['class' => 'form-control', 'maxLength' => 250]) !!}
    </div>
</div>

<div class="text-right">
    {!! Form::submit($example->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}
