@extends('home.layout')

@section('home-title') Breeding Permissions @endsection

@section('home-content')
{!! breadcrumbs(['Breeding Permissions' => 'breeding-permissions']) !!}

<h1>Breeding Permissions</h1>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('used') || Request::get('used') == 0 ? 'active' : '' }}" href="{{ url('breeding-permissions') }}">Unused</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('used') == 1 ? 'active' : '' }}" href="{{ url('breeding-permissions') . '?used=1' }}">Used</a>
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
