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

    {!! Form::open(['url' => $generation->id ? 'admin/data/character-generations/edit/' . $generation->id : 'admin/data/character-generations/create']) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name (Required)') !!}
        {!! Form::text('name', $generation->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!} {!! add_help('Currently this serves no purpose, but you are welcome to write notes and whatever else you would like here.') !!}
        {!! Form::textarea('description', $generation->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($generation->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($generation->id)
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
