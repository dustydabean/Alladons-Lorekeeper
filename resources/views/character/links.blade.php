@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Links @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Profile' => $character->url . '/profile']) !!}

<h2 class="bold">{{$character->fullName}}'s Links</h2>
@foreach($character->link as $Clink)
<div class="container">
        <div class="d-flex justify-content-around">
            @dd($Clink->characters)
            @foreach($Clink->characters as $character)
            @dd($character)
                <img src="{{ $character->character->image->thumbnailUrl }}">
            @endforeach
        </div>
    <div class="container">

</div>
@endforeach

{{-- Bio --}}
<a class="float-left" href="{{ url('reports/new?url=') . $character->url . '/links' }}"><i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this character's links." style="opacity: 50%;"></i></a>
@if(Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
    <div class="text-right mb-2">
        <a href="{{ $character->url . '/links/edit' }}" class="btn btn-outline-info btn-sm"><i class="fas fa-envelope"></i> Request Links</a>
    </div>
@endif
@endsection