@extends('world.layout')

@section('title')
    Character Generations
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Character Generations' => 'world/pedigrees']) !!}
    <h1>Character Generations</h1>

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

    {!! $generations->render() !!}
    @foreach ($generations as $generation)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $generation->imageUrl, 'name' => $generation->displayName, 'description' => $generation->description, 'searchUrl' => $generation->searchUrl, 'edit' => ['title' => 'Generation', 'object' => $generation]])
            </div>
        </div>
    @endforeach
    {!! $generations->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $generations->total() }} result{{ $generations->total() == 1 ? '' : 's' }} found.</div>
@endsection
