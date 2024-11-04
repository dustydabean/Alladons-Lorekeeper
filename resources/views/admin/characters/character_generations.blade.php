@extends('admin.layout')

@section('admin-title')
    Character Generations
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Character Generations' => 'admin/data/character-generations']) !!}

    <h1>Character Generations</h1>

    <p>Here you can create and edit generations that are available from the dropdown menu when creating or editing a character.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/data/character-generations/create') }}"><i class="fas fa-plus"></i> Create New Character Generation</a>
    </div>
    @if (!count($generations))
        <p>No character generations found.</p>
    @else
        {!! $generations->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-12">
                        <div class="logs-table-cell">Generation</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($generations as $generation)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-10">
                                <div class="logs-table-cell">{!! $generation->name !!}</div>
                            </div>
                            <div class="col-2 text-right">
                                <div class="logs-table-cell"><a href="{{ url('admin/data/character-generations/edit/' . $generation->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $generations->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $generations->total() }} result{{ $generations->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
