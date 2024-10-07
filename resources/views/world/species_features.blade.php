@extends('world.layout')

@section('world-title')
    {{ $species->name }} Mutations
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Species' => 'world/species', $species->name => $species->url, 'Traits' => 'world/species/' . $species->id . 'traits']) !!}
    <h1>{{ $species->name }} Mutations</h1>

    <p>This is a visual index of all {!! $species->displayName !!}-specific mutations. Click a mutation to view more info on it!</p>

    @include('world._features_index', ['features' => $features, 'showSubtype' => true])
@endsection

@section('scripts')
    @if (config('lorekeeper.extensions.visual_trait_index.trait_modals'))
        @include('world._features_index_modal_js')
    @endif
@endsection
