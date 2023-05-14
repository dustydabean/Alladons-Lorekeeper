@extends('admin.layout')

@section('admin-title')
    Species
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Transformations' => 'admin/data/transformations',
        ($transformation->id ? 'Edit' : 'Create') . ' Transformation' => $transformation->id ? 'admin/data/transformations/edit/' . $transformation->id : 'admin/data/transformations/create',
    ]) !!}

    <h1>{{ $transformation->id ? 'Edit' : 'Create' }} Transformation
        @if ($transformation->id)
            <a href="#" class="btn btn-danger float-right delete-transformation-button">Delete Transformation</a>
        @endif
    </h1>

    {!! Form::open(['url' => $transformation->id ? 'admin/data/transformations/edit/' . $transformation->id : 'admin/data/transformations/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $transformation->name, ['class' => 'form-control']) !!}
    </div>



    <div class="form-group">
        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($transformation->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $transformation->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($transformation->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($transformation->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._transformation_entry', ['transformation' => $transformation])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-transformation-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/transformations/delete') }}/{{ $transformation->id }}", 'Delete Transformation');
            });
        });
    </script>
@endsection
