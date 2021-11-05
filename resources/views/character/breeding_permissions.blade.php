@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }} @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
@if($character->is_myo_slot)
{!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url]) !!}
@else
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url]) !!}
@endif

@include('character._header', ['character' => $character])

@if(Auth::check() && (Auth::user()->id == $character->user_id))
    <div class="text-right mb-4">
        <a href="#" class="btn btn-success create-breeding-permission">Create New Permission</a>
    </div>
@endif

<p>
    This character has {{ $character->availableBreedingPermissions }} out of {{ $character->maxBreedingPermissions }} maximum breeding permission{{ $character->availableBreedingPermissions == 1 ? '' : 's' }} available to create.
    @if(Auth::check() && (Auth::user()->id == $character->user_id))
        As the character's owner, you may create and grant to other users up to this many breeding permissions. Other users may see how many of this character's breeding permissions have been created and/or used, and to whom they have been granted.
    @else
        Only the character's owner can create and distribute breeding permissions.
    @endif
</p>

@if($permissions->count())
    {!! $permissions->render() !!}

    @foreach($permissions as $permission)
        @include('character._breeding_permission', ['isCharacter' => true])
    @endforeach

    {!! $permissions->render() !!}
@else
    <p>No permissions found.</p>
@endif

@endsection

@section('scripts')
    @parent
    @include('character._breeding_permissions_js')
@endsection
