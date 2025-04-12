@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Breeding Permissions @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Breeding Permissions' => $user->url . '/breeding-permissions']) !!}

<h1>
    {!! $user->displayName !!}'s Breeding Permissions
</h1>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('used') || Request::get('used') == 0 ? 'active' : '' }}" href="{{ $user->url.'/breeding-permissions' }}">Unused</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('used') == 1 ? 'active' : '' }}" href="{{ $user->url.'/breeding-permissions' . '?used=1' }}">Used</a>
    </li>
</ul>

@if(count($permissions))
    {!! $permissions->render() !!}

    @foreach($permissions as $permission)
        @include('character._breeding_permission', ['isCharacter' => false, 'character' => $permission->character])
    @endforeach

    {!! $permissions->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $permissions->total() }} result{{ $permissions->total() == 1 ? '' : 's' }} found.</div>
@else
    <p>No breeeding permissions found.</p>
@endif

@endsection

@section('scripts')
    @parent
    @include('character._breeding_permissions_js')
@endsection
