@extends('world.layout')

@section('world-title')
    Universal Trait Index
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Universal Trait Index' => 'world/universaltraits']) !!}
    <h1>Universal Mutation Index</h1>

    <p>This is a visual index of all mutations. Click a mutation to view more info on it!</p>

    <p><strong>Helpful Info</strong></p>
    <p>  ▹ "Expressing/Full" : Parent has the mutation</p>
    <p>  ▹ "Non Carrier" : Parent does not have the mutation</p>
    <p>  ▹ "No" : Means no Major muts are present (minor muts are ok)</p>
    <p>  ▹ "[E,D,F]" : Only Full content of that species can breed that mutation</p>
    <p>  ▹ ' or " : How many spectrums (Primary, Secondary, Tertiary)</p>
    <p>   ' = one spectrum</p>
    <p>   " = two spectrums</p>
    <p>   ''' = three spectrums</p>

    @include('world._features_index', ['features' => $features, 'showSubtype' => false])
@endsection

@section('scripts')
    @if (config('lorekeeper.extensions.visual_trait_index.trait_modals'))
        @include('world._features_index_modal_js')
    @endif
@endsection

<p>  ▹ </p>