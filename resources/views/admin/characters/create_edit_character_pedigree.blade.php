@extends('admin.layout')

@section('admin-title')
    {{ $pedigree->id ? 'Edit' : 'Create' }} Character Pedigree
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Character Pedigrees' => 'admin/data/character-pedigrees',
        ($pedigree->id ? 'Edit' : 'Create') . ' Pedigree' => $pedigree->id ? 'admin/data/character-pedigrees/edit/' . $pedigree->id : 'admin/data/character-pedigrees/create',
    ]) !!}

    <h1>{{ $pedigree->id ? 'Edit' : 'Create' }} Character Pedigree
        @if ($pedigree->id)
            <a href="#" class="btn btn-danger float-right delete-pedigree-button">Delete Pedigree</a>
        @endif
    </h1>

    {!! Form::open(['url' => $pedigree->id ? 'admin/data/character-pedigrees/edit/' . $pedigree->id : 'admin/data/character-pedigrees/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name (Required)') !!}
        {!! Form::text('name', $pedigree->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($pedigree->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $pedigree->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($pedigree->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($pedigree->id)
        <h3>Preview</h3>
        <div class="card mt-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $pedigree->imageUrl, 'name' => $pedigree->displayName, 'description' => $pedigree->description, 'searchUrl' => $pedigree->searchUrl])
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-header h2">Pedigree Details</div>
            <div class="card-body pb-2">
                @if (!$characters->count())
                    <p class="text-muted">No characters have this pedigree tag.</p>
                @else
                    <h5>Characters with this Pedigree Tag</h5>
                    <ul>
                        @foreach ($characters as $character)
                            <li>
                                @if (!$character->is_visible)
                                    <i class="fas fa-eye-slash mr-1"></i>
                                @endif
                                {!! $character->displayName !!} - ({!! $character->pedigreeName !!})
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
            $('.delete-pedigree-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/character-pedigrees/delete') }}/{{ $pedigree->id }}", 'Delete Pedigree');
            });
        });
    </script>
@endsection
