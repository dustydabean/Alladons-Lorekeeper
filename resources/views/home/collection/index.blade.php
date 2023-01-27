@extends('home.layout')

@section('home-title') Collection @endsection

@section('home-content')
{!! breadcrumbs(['Collection' => 'collection']) !!}

<h1>
    Collection Gallery
</h1>
<p> This is a list of all collections, as well as your completed collections. </p>

<hr>

<h3>Incomplete Collections</h3>
@if($collections->count())
    <div class="row mx-0">
        @foreach($collections as $collection)
            @include('home.collection._smaller_collection_card', ['collection' => $collection])
        @endforeach
    </div>
@else
    You've completed all collections!
@endif

<hr>

<h3>Your Completed Collections</h3>
@if(Auth::user()->collections->count())
    <div class="row mx-0">
        @foreach(Auth::user()->collections as $collection)
            @include('home.collection._smaller_collection_card', ['collection' => $collection])
        @endforeach
    </div>
@else
    You haven't completed any collections!
@endif
<div class="text-right mb-4">
    <a href="{{ url(Auth::user()->url.'/collection-logs') }}">View logs...</a>
</div>


@endsection


@section('scripts')
<script>
$( document ).ready(function() {
    $('.btn-craft').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent().parent();
        loadModal("{{ url('collection/complete') }}/" + $parent.data('id'), $parent.data('name'));
    });
});
</script>
@endsection