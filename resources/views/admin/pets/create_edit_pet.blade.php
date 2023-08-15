@extends('admin.layout')

@section('admin-title') Pets @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pets' => 'admin/data/pets', ($pet->id ? 'Edit' : 'Create').' Pet' => $pet->id ? 'admin/data/pets/edit/'.$pet->id : 'admin/data/pets/create']) !!}

<h1>
    {{ $pet->id ? 'Edit' : 'Create' }} Pet
    @if($pet->id)
        <a href="#" class="btn btn-outline-danger float-right delete-pet-button">Delete Pet</a>
        @if ($pet->dropData)
            <a href="{{ url('/admin/data/pets/drops/edit/') . '/' . $pet->id }}" class="btn btn-info float-right mr-2">Edit Drops</a>
        @else
            <a href="{{ url('/admin/data/pets/drops/create') }}" class="btn btn-info float-right mr-2">Create Drops</a>
        @endif
    @endif
</h1>

{!! Form::open(['url' => $pet->id ? 'admin/data/pets/edit/'.$pet->id : 'admin/data/pets/create', 'files' => true]) !!}

@if(!$pet->id)<p>You can create variants once the pet is made.<p>@endif

<h2>Basic Information</h2>

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

<div class="form-group row no-gutters align-items-center">
    {!! Form::label('pet_category_id', 'Pet Category (Optional)', ['class' => 'col-md mb-0']) !!}
    {!! Form::select('pet_category_id', $categories, $pet->pet_category_id, ['class' => 'col-md-9 form-control']) !!}
</div>

<div class="form-group row no-gutters align-items-center">
    <div class="col-md col-form-label">
        {!! Form::label('limit', 'Character Hold Limit (Optional)', ['class' => 'mb-0']) !!} {!! add_help('This limit is per pet and holds lower priority than category limits, if set. If there is a category set, it is only applicable if that category can be attached.') !!}
    </div>
    {!! Form::number('limit', $pet->limit, ['class' => 'col-md-9 form-control px-2']) !!}
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
    <h2>Variants</h2>
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-2 text-right">
                <a href="#" class="btn btn-primary" id="add-variant">Add Variant</a>
            </div>
            @if ($pet->variants->count())
                @foreach($pet->variants->chunk(4) as $chunk)
                    <div class="row">
                        @foreach($chunk as $variant)
                            <div class="col"><div class="card h-100 mb-3"><div class="card-body text-center">
                                @if($variant->has_image)
                                    <a href="{{ $variant->imageUrl }}" data-lightbox="entry" data-title="{{ $variant->variant_name }}">
                                        <img src="{{ $variant->imageUrl }}" class="img-fluid" alt="{{ $variant->variant_name }}" data-toggle="tooltip" data-title="{{ $variant->variant_name }}" style="max-height:200px" />
                                    </a>
                                    <div class="h5 my-2">{{ $variant->variant_name }}</div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-primary edit-variant" data-id="{{$variant->id}}"><i class="fas fa-cog mr-1"></i>Edit</a>
                                        @if($variant->dropData)
                                            <a href="{{ url('/admin/data/pets/drops/edit/') . '/' . $pet->id.'#variant-'.$variant->id }}" class="btn btn-sm btn-primary"><i class="fas fa-gift mr-1"></i>Drops</a>
                                        @endif
                                    </div>
                                @else
                                    {{ $variant->name }}
                                @endif
                            </div></div></div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">No variants found.</div>
            @endif
        </div>
    </div>

    <div class="feature-row hide mb-2">
        {!! Form::text('variant_names[]', null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Variant Name']) !!}
        {!! Form::file('variant_images[]') !!}
        <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
    </div>

    <h2>Preview</h2>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._pet_entry', ['pet' => $pet])
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

    $('#add-variant').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/pets/edit/'.$pet->id.'/variants/create') }}", 'Create Variant');
    });

    $('.edit-variant').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/pets/edit/'.$pet->id.'/variants/edit') }}/" + $(this).data('id'), 'Edit Variant');
    });
});

</script>
@endsection
