@extends('world.layout')

@section('title') Collections @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Collections' => 'world/collections']) !!}
<h1>Collections</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
            {!! Form::select('collection_category_id', $categories, Request::get('collection_category_id'), ['class' => 'form-control']) !!}
        </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First',
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $collections->render() !!}
@foreach($collections as $collection)
    <div class="card mb-3">
        <div class="card-body">

        @include('world.collections._collection_entry', ['collection' => $collection, 'imageUrl' => $collection->imageUrl, 'name' => $collection->displayName, 'description' => $collection->parsed_description])
        </div>
    </div>
@endforeach
{!! $collections->render() !!}

<div class="text-center mt-4 small text-muted">{{ $collections->total() }} result{{ $collections->total() == 1 ? '' : 's' }} found.</div>

@endsection