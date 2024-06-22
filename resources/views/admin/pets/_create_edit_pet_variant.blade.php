{!! Form::open(['url' => 'admin/data/pets/edit/' . $pet->id . '/variants/' . ($variant->id ? 'edit/' . $variant->id : 'create'), 'files' => true]) !!}

<div class="form-group">
    {!! Form::text('variant_name', $variant->id ? $variant->variant_name : null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Variant Name']) !!}
</div>

<div class="form-group">
    {!! Form::label('Image (Optional)') !!}
    <div>{!! Form::file('variant_image') !!}</div>
    <div class="text-muted">Recommended size: 200px x 200px</div>
    @if ($variant->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $variant->id ? $variant->description : null, ['class' => 'form-control wysiwyg']) !!}
</div>

@if ($variant->id)
    <div class="form-check">
        {!! Form::checkbox('delete', 1, false, ['class' => 'form-check-input']) !!}
        {!! Form::label('delete', 'Delete Variant', ['class' => 'form-check-label']) !!}
    </div>
@endif

<div class="text-right">
    {!! Form::submit($variant->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}
