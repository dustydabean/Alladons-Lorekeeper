@extends('admin.layout')

@section('admin-title')
    {{ $generation->id ? 'Edit' : 'Create' }} Character Generation
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Character Generations' => 'admin/data/character-generations',
        ($generation->id ? 'Edit' : 'Create') . ' Generation' => $generation->id ? 'admin/data/character-generations/edit/' . $generation->id : 'admin/data/character-generations/create',
    ]) !!}

    <h1>{{ $generation->id ? 'Edit' : 'Create' }} Character Generation
        @if ($generation->id)
            <a href="#" class="btn btn-danger float-right delete-generation-button">Delete Generation</a>
        @endif
    </h1>

    {!! Form::open(['url' => $generation->id ? 'admin/data/character-generations/edit/' . $generation->id : 'admin/data/character-generations/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name (Required)') !!}
        {!! Form::text('name', $generation->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($generation->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $generation->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($generation->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($generation->id)
        <h3>Preview</h3>
        <div class="card mt-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $generation->imageUrl, 'name' => $generation->displayName, 'description' => $generation->description, 'searchUrl' => $generation->searchUrl])
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-header h2">Generation Details</div>
            <div class="card-body pb-2">
                @if (!$characters->count())
                    <p class="text-muted">No characters have this generation.</p>
                @else
                    <h5>Characters with this Generation</h5>
                    <ul>
                        @foreach ($characters as $character)
                            <li>
                                @if (!$character->is_visible)
                                    <i class="fas fa-eye-slash mr-1"></i>
                                @endif
                                {!! $character->displayName !!}
                            </li>
                        @endforeach
                    </ul>
                    <div class="text-center mt-2 small text-muted">{{ $characters->count() }} character{{ $characters->count() == 1 ? '' : 's' }} found.</div>
                @endif
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-generation-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/character-generations/delete') }}/{{ $generation->id }}", 'Delete Generation');
            });
        });
    </script>
@endsection
