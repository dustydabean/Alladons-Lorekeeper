@extends('admin.layout')

@section('admin-title') Collections @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Collections' => 'admin/data/collections', ($collection->id ? 'Edit' : 'Create').' Collection' => $collection->id ? 'admin/data/collections/edit/'.$collection->id : 'admin/data/collections/create']) !!}

<h1>{{ $collection->id ? 'Edit' : 'Create' }} Collection
    @if($collection->id)
        <a href="#" class="btn btn-outline-danger float-right delete-collection-button">Delete Collection</a>
    @endif
</h1>

{!! Form::open(['url' => $collection->id ? 'admin/data/collections/edit/'.$collection->id : 'admin/data/collections/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $collection->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 100px x 100px</div>
    @if($collection->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>
<div class="row">
    <div class="col-md-8">
<div class="form-group">
            {!! Form::label('Collection Category (Optional)') !!}
            {!! Form::select('collection_category_id', $collectioncategories, $collection->collection_category_id, ['class' => 'form-control selectize']) !!}
</div>
<div class="form-group">
    {!! Form::checkbox('is_visible', 1, $collection->id ? $collection->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the collection will not be visible.') !!}
</div>
</div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="form-group">
            {!! Form::label('Collection Parent (Optional)') !!} {!! add_help('A parent collection means the user will be required to complete the parent before they can complete this collection.') !!}
            {!! Form::select('parent_id', $collections, $collection->parent_id, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $collection->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>Collection Requirements</h3>
@include('widgets._collection_ingredient_select', ['ingredients' => $collection->ingredients])

<hr>

<h3>Collection Rewards</h3>
@include('widgets._collection_reward_select', ['rewards' => $collection->rewards])

<div class="text-right">
    {!! Form::submit($collection->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._collection_ingredient_select_row', ['items' => $items, 'categories' => $categories, 'currencies' => $currencies])
@include('widgets._collection_reward_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables, 'raffles' => $raffles])

@if($collection->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
        @include('world.collections._collection_entry', ['collection' => $collection, 'imageUrl' => $collection->imageUrl, 'name' => $collection->displayName, 'description' => $collection->parsed_description])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
@include('js._collection_reward_js')
@include('js._collection_ingredient_js')
<script>
$( document ).ready(function() {    
    $('.delete-collection-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/collections/delete') }}/{{ $collection->id }}", 'Delete Collection');
    });
});
    
</script>
@endsection