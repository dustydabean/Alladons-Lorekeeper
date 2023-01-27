@extends('admin.layout')

@section('admin-title') Collections @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Collections' => 'admin/data/collections']) !!}

<h1>Collections</h1>

<p>This is a list of collections in the game that users can complete for rewards.</p> 
<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/collections/collection-categories') }}"><i class="fas fa-folder"></i> Collection Categories</a></div>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/collections/create') }}"><i class="fas fa-plus"></i> Create New Collection</a></div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('collection_category_id', $categories, Request::get('collection_category_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('is_visible', $is_visible, Request::get('is_visible'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($collections))
    <p>No collections found.</p>
@else 
    {!! $collections->render() !!}
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Is Visible</th>
                <th>Category</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $collection)
                <tr class="sort-item" data-id="{{ $collection->id }}">
                    <td>
                        {{ $collection->name }}
                    </td>
                    <td>{{ $collection->is_visible ? 'Active' : '' }}</td>
                    <td> {{ $collection->category ? $collection->category->name : '' }} </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/collections/edit/'.$collection->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $collections->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection