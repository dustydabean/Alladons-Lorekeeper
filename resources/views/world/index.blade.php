@extends('world.layout')

@section('world-title')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world']) !!}

    <h1>Information</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('images/characters.png') }}" alt="Characters" />
                    <h5 class="card-title">Alladons</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="{{ url('world/species') }}">Species</a></li>
                    <li class="list-group-item"><a href="{{ url('world/subtypes') }}">Species Content</a></li>
                    <li class="list-group-item"><a href="{{ url('world/character-categories') }}">Species Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/character-pedigrees') }}">Alladon Pedigrees</a></li>
                    <!--<li class="list-group-item"><a href="{{ url('world/character-generations') }}">Character Generations</a></li>-->
                    <li class="list-group-item"><a href="{{ url('world/transformations') }}">Transformations</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('images/inventory.png') }}" alt="Items" />
                    <h5 class="card-title">Mutations</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @if (config('lorekeeper.extensions.visual_trait_index.enable_universal_index'))
                        <li class="list-group-item"><a href="{{ url('world/universaltraits') }}">Mutation Index</a></li>
                    @endif
                    <li class="list-group-item"><a href="{{ url('world/trait-categories') }}">Mutation Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/rarities') }}">Mutation Points</a></li>
                    <!--<li class="list-group-item"><a href="{{ url('world/traits') }}">All Mutations</a></li>-->
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('images/inventory.png') }}" alt="Items" />
                    <h5 class="card-title">Items & Companions</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="{{ url('world/item-categories') }}">Item Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/items') }}">All Items</a></li>
                    <li class="list-group-item"><a href="{{ url('world/currencies') }}">Currencies</a></li>
                    <!--<li class="list-group-item"><a href="{{ url('world/pet-categories') }}">Pet Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/pets') }}">All Pets</a></li>-->
                    <li class="list-group-item"><a href="{{ url('world/collections') }}">Collections</a></li>
                    <li class="list-group-item"><a href="{{ url('world/recipes') }}">All Recipes</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection
