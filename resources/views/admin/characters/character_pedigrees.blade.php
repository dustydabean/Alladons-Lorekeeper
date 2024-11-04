@extends('admin.layout')

@section('admin-title')
    Character Pedigrees
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Character Pedigrees' => 'admin/data/character-pedigrees']) !!}

    <h1>Character Pedigrees</h1>

    <p>Here you can create and edit pedigree tags that are available from the dropdown menu when creating or editing a character.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/data/character-pedigrees/create') }}"><i class="fas fa-plus"></i> Create New Character Pedigree</a>
    </div>
    @if (!count($pedigrees))
        <p>No character pedigrees found.</p>
    @else
        {!! $pedigrees->render() !!}
        <div class="mb-4 logs-table">
            <div class="logs-table-header">
                <div class="row">
                    <div class="col-12">
                        <div class="logs-table-cell">Pedigree</div>
                    </div>
                </div>
            </div>
            <div class="logs-table-body">
                @foreach ($pedigrees as $pedigree)
                    <div class="logs-table-row">
                        <div class="row flex-wrap">
                            <div class="col-10">
                                <div class="logs-table-cell">{!! $pedigree->name !!}</div>
                            </div>
                            <div class="col-2 text-right">
                                <div class="logs-table-cell"><a href="{{ url('admin/data/character-pedigrees/edit/' . $pedigree->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        {!! $pedigrees->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $pedigrees->total() }} result{{ $pedigrees->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection
