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
@if(Auth::user()->incompletedCollections->count())
<div class="card character-bio">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            @foreach($incomplete as $categoryId=>$categorycollections)
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="categoryTab-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" data-toggle="tab" href="#collectiontestincomp-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" role="tab">
                        {!! isset($categories[$categoryId]) ? $categories[$categoryId]->name : 'Miscellaneous' !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body tab-content">
    @foreach($incomplete as $categoryId=>$categorycollections)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="collectiontestincomp-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}">
                    @foreach($categorycollections->chunk(4) as $incomplete)
                <div class="row mb-3">
                    @foreach($incomplete as $collection)
            @include('home.collection._smaller_collection_card', ['collection' => $collection])
                    @endforeach
                </div>
            @endforeach
       </div>
@endforeach
</div>
    </div>
@else
    You've completed all current collections!
@endif

<hr>

<h3>Your Completed Collections</h3>
@if(Auth::user()->collections->count())
<div class="card character-bio">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            @foreach($collections as $categoryId=>$categorycollections)
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="categoryTab-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" data-toggle="tab" href="#collectiontest-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" role="tab">
                        {!! isset($categories[$categoryId]) ? $categories[$categoryId]->name : 'Miscellaneous' !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body tab-content">
    @foreach($collections as $categoryId=>$categorycollections)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="collectiontest-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}">
                    @foreach($categorycollections->chunk(4) as $collection)
                <div class="row mb-3">
                    @foreach($collection as $collection)
            @include('home.collection._smaller_collection_card', ['collection' => $collection])
                    @endforeach
                </div>
            @endforeach
       </div>
@endforeach
            </div>
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