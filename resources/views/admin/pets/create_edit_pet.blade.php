@extends('admin.layout')

@section('admin-title') Pets @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pets' => 'admin/data/pets', ($pet->id ? 'Edit' : 'Create').' Pet' => $pet->id ? 'admin/data/pets/edit/'.$pet->id : 'admin/data/pets/create']) !!}

<h1>{{ $pet->id ? 'Edit' : 'Create' }} Pet
    @if($pet->id)
        <a href="#" class="btn btn-outline-danger float-right delete-pet-button">Delete Pet</a>
    @endif
</h1>

{!! Form::open(['url' => $pet->id ? 'admin/data/pets/edit/'.$pet->id : 'admin/data/pets/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $pet->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 100px x 100px</div>
    @if($pet->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>
<div class="form-group">
    {!! Form::label('Pet Category (Optional)') !!}
    {!! Form::select('pet_category_id', $categories, $pet->pet_category_id, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $pet->description, ['class' => 'form-control wysiwyg']) !!}
</div>

{!! Form::checkbox('allow_transfer', 1, $pet->id ? $pet->allow_transfer : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
{!! Form::label('allow_transfer', 'Allow User → User Transfer', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is off, users will not be able to transfer this pet to other users. Non-account-bound pets can be account-bound when granted to users directly.') !!}

<div class="text-right">
    {!! Form::submit($pet->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($pet->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._entry', ['imageUrl' => $pet->imageUrl, 'name' => $pet->displayName, 'description' => $pet->parsed_description, 'searchUrl' => $pet->searchUrl])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-pet-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/pets/delete') }}/{{ $pet->id }}", 'Delete Pet');
    });
});
    
</script>
@endsection