@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Characters
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Characters' => $user->url . '/characters']) !!}

<h1>
    {!! $user->displayName !!}'s Alladons
</h1>

@if($characters->count())
    @foreach($characters as $key => $group)
        <div class="card mb-3 inventory-category">
            <a href="{{ $group->first()->folder ? $group->first()->folder->url : '#' }}">
                <h5 class="card-header inventory-header">
                    <span data-toggle="tooltip" title="{{ $group->first()->folder ? $group->first()->folder->description : 'Characters without a folder.'}}">{{ $key }}</span>
                </h5>
            </a>
            
            <div class="card-body inventory-body">
                <div class="row mb-2">
                    @foreach($group as $character)
                        <div class="col-md-3 col-6 text-center mb-2">
                            <div>
                                <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}" /></a>
                            </div>
                            <div class="mt-1 h5">
                                @if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $character->displayName !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
@else
    <p>No characters found.</p>
@endif

    @include('user._characters', ['characters' => $characters, 'myo' => false])
@endsection
