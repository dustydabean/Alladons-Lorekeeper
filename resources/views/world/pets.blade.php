@extends('world.layout')

@section('title')
    Pets
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Pets' => 'world/pets']) !!}
    <h1>Pets</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('pet_category_id', $categories, Request::get('pet_category_id'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select(
                    'sort',
                    [
                        'alpha' => 'Sort Alphabetically (A-Z)',
                        'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                        'category' => 'Sort by Category',
                        'newest' => 'Newest First',
                        'oldest' => 'Oldest First',
                    ],
                    Request::get('sort') ?: 'category',
                    ['class' => 'form-control'],
                ) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    {!! $pets->render() !!}
    @foreach ($pets as $pet)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._pet_entry', ['pet' => $pet])
            </div>
        </div>
    @endforeach
    {!! $pets->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $pets->total() }} result{{ $pets->total() == 1 ? '' : 's' }} found.</div>
@endsection
