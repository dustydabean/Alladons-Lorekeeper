@extends('world.layout')

@section('world-title')
    Mutations
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Traits' => 'world/traits']) !!}
    <h1>Mutations</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('code_id', Request::get('code_id'), ['class' => 'form-control', 'placeholder' => 'Mutation Code']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <!--<div class="form-group ml-3 mb-3">
                {!! Form::select('species_id', $specieses, Request::get('species_id'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('subtype_id', $subtypes, Request::get('subtype_id'), ['class' => 'form-control']) !!}
            </div>-->
            <div class="form-group ml-3 mb-3">
                {!! Form::select('rarity_id', $rarities, Request::get('rarity_id'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('feature_category_id', $categories, Request::get('feature_category_id'), ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('mut_level', $levels, Request::get('mut_level'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('mut_type', $types, Request::get('mut_type'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-check ml-3 mb-3">
                {!! Form::select('is_locked', ['none' => 'Any Status', '0' => 'Unlocked', '1' => 'Locked'], Request::get('is_locked'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select(
                    'sort',
                    [
                        'oldest' => 'Oldest First (Reccommended)',
                        'newest' => 'Newest First',
                        'alpha' => 'Sort Alphabetically (A-Z)',
                        'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                        'category' => 'Sort by Category',
                        'rarity-reverse' => 'Sort by Rarity (Common to Rare)',
                        'rarity' => 'Sort by Rarity (Rare to Common)',
                        'species' => 'Sort by Species',
                        'subtypes' => 'Sort by Subtype',
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

    {!! $features->render() !!}
    @foreach ($features as $feature)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._feature_entry', ['feature' => $feature])
            </div>
        </div>
    @endforeach
    {!! $features->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $features->total() }} result{{ $features->total() == 1 ? '' : 's' }} found.</div>
@endsection
