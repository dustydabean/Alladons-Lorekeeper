@extends('world.layout')

@section('title') {{ $collection->name }} @endsection

@section('meta-img') {{ $collection->imageUrl ? $collection->imageUrl : null }} @endsection

@section('meta-desc')
    {!! substr(str_replace('"','&#39;',$collection->description),0,69) !!}
@endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'collections' => 'world/collections', $collection->name => $collection->idUrl]) !!}

<div class="card mb-3">
        <div class="card-body">

        @include('world.collections._collection_entry', ['collection' => $collection, 'imageUrl' => $collection->imageUrl, 'name' => $collection->displayName, 'description' => $collection->parsed_description])
        </div>
    </div>
@endsection