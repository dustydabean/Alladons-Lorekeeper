@extends('world.layout')

@section('title') {{ $collection->name }} @endsection

@section('meta-img') {{ $collection->imageUrl ? $collection->imageUrl : null }} @endsection

@section('meta-desc')
    {!! substr(str_replace('"','&#39;',$collection->description),0,69) !!}
@endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'collections' => 'world/collections', $collection->name => $collection->idUrl]) !!}

<div class="row">
    <div class="col-lg-6 col-lg-10 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                @if($collection->imageUrl)
                    <div class="world-entry-image text-center mb-2"><a href="{{ $collection->imageUrl }}" data-lightbox="entry" data-title="{{ $collection->name }}"><img src="{{ $collection->imageUrl }}" class="world-entry-image mw-100" style="max-height:300px;" /></a></div>
                @endif

                <div>
                    <h1>
                        
                        {!! $collection->name !!}
                    </h1>
                    <div class="world-entry-text">
                        {!! $collection->description !!}
                    </div>

                    <div class="row">

                       

                        <div class="col-md-6">
                            <h5>Ingredients</h5>
                            @foreach($collection->ingredients as $ingredient)
                                <div class="alert alert-secondary">
                                    @include('home.collection._collection_ingredient_entry', ['ingredient' => $ingredient])
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <h5>Rewards</h5>
                            @foreach($collection->reward_items as $type)
                                @foreach($type as $item)
                                    <div class="alert alert-secondary">
                                        @include('home.collection._collection_reward_entry', ['reward' => $item])
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection