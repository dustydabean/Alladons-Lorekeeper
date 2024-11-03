@extends('world.layout')

@section('title')
    Character Pedigrees
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Character Pedigrees' => 'world/pedigrees']) !!}
    <h1>Character Pedigrees</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
            <div class="form-group mr-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        {!! Form::close() !!}
    </div>

    {!! $pedigrees->render() !!}
    @foreach ($pedigrees as $pedigree)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $pedigree->imageUrl, 'name' => $pedigree->displayName, 'description' => $pedigree->description, 'searchUrl' => $pedigree->searchUrl, 'edit' => ['title' => 'Pedigree', 'object' => $pedigree]])
            </div>
        </div>
    @endforeach
    {!! $pedigrees->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $pedigrees->total() }} result{{ $pedigrees->total() == 1 ? '' : 's' }} found.</div>
@endsection
