{!! Form::open(['url' => 'admin/data/pets/edit/' . $pet->id . '/evolution/' . ($evolution->id ? 'edit/' . $evolution->id : 'create'), 'files' => true]) !!}

<div class="form-group">
    {!! Form::text('evolution_name', $evolution->id ? $evolution->evolution_name : null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Evolution Name (Required)']) !!}
</div>

<p>
    Enter the level at which this art is shown. When a companion reaches this <u>exact</u> level, its art automatically displays this evolution art. At any other level it shows the base companion art.
</p>

<div class="form-group">
    {!! Form::label('Level (Required)') !!}
    {!! Form::number('evolution_stage', $evolution->id ? $evolution->evolution_stage : null, ['class' => 'form-control', 'placeholder' => 'Level (Number)', 'min' => 1]) !!}
</div>

<div class="form-group">
    {!! Form::label($evolution->id ? 'Image' : 'Image (Required)') !!}
    <div class="custom-file">
        {!! Form::label('evolution_image', 'Choose file...', ['class' => 'custom-file-label']) !!}
        {!! Form::file('evolution_image', ['class' => 'custom-file-input']) !!}
    </div>
    <div class="text-muted">Recommended size: 200px x 200px</div>

    @if ($evolution->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="text-right">
    {!! Form::submit($evolution->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
