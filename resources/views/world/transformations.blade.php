@extends('world.layout')

@section('title')
    Transformations
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Transformations' => 'world/transformations']) !!}
    <h1>Transformations</h1>

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

    {!! $transformations->render() !!}
    @foreach ($transformations as $transformation)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._transformation_entry', ['transformation' => $transformation])
            </div>
        </div>
    @endforeach
    {!! $transformations->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $transformations->total() }} result{{ $transformations->total() == 1 ? '' : 's' }} found.</div>
@endsection
