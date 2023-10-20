@extends('world.layout')

@section('title')
    Pet Categories
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Pet Categories' => 'world/pet-categories']) !!}
    <h1>
        Pet Categories</h1>

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

    {!! $categories->render() !!}
    @foreach ($categories as $category)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $category->categoryImageUrl, 'name' => $category->displayName, 'description' => $category->parsed_description, 'searchUrl' => $category->searchUrl])
                @if ($category->allow_attach && (!isset($category->limit) || $category->limit > 0))
                    <div class="alert alert-info mb-0 mt-2">Can be attached to characters @isset($category->limit)
                            <b>— up to {{ $category->limit }} per pet</b>
                        @endisset
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    {!! $categories->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $categories->total() }} result{{ $categories->total() == 1 ? '' : 's' }} found.</div>
@endsection
