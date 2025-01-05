@extends('user.layout')

@section('profile-title') {{ $user->name }}'s {{ $folder->name }} Characters @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Characters' => $user->url . '/characters', $folder->name => '/'.$folder->name]) !!}

<h1>
    {!! $user->displayName !!}'s {{ $folder->name }} Alladons
</h1>

@if ($characters->count())
    <div class="card mb-3 inventory-category">
        <a href="{{ $folder->url }}">
            <h5 class="card-header inventory-header">
                <span data-toggle="tooltip" title="{{ $folder->description }}">{{ $folder->name }}</span>
            </h5>
        </a>
        
        <div class="card-body inventory-body">
            @include('user._characters', ['characters' => $characters, 'myo' => false, 'owner' => true, 'userpage_exts' => true, 'folders' => false])
        </div>
    </div>
@else
    <p>No characters found.</p>
@endif

@endsection