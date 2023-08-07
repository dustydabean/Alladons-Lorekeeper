@php $namespace = (Request::url() == url('pets/view/'.$pet->id)); @endphp
@extends(($namespace ? 'home' : 'user').'.layout')

@section($namespace ? 'title' : 'profile-title')
    {{ $pet->pet_name ? $pet->pet_name . ' ('.$pet->pet->name.')' : $user->name."'s ".$pet->pet->name }}
@endsection

@section($namespace ? 'content' : 'profile-content')

    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Pets' => $user->url . '/pets',
        $pet->pet_name ? $pet->pet_name . ' ('.$pet->pet->name.')' : $user->name."'s ".$pet->pet->name => $pet->url]) !!}

    <h1>
        {{ $pet->pet_name ? $pet->pet_name . ' ('. $user->name."'s ". $pet->pet->name.')' : $user->name."'s ".$pet->pet->name }}
    </h1>

    @if(Auth::check() && ($pet->user_id !== Auth::user()->id && Auth::user()->hasPower('edit_inventories')))
        <div class="alert alert-warning">
            You are editing this pet as a staff member.
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body text-center">
            <img class="img-fluid" src="{{ $pet->pet->VariantImage($pet->id) }}" data-toggle="tooltip" title="{{ $pet->pet_name ?? $pet->pet->name }}" alt="{{ $pet->pet_name ?? $pet->pet->name }}" />
            @if($pet->has_image)
                <div class="mt-2">
                    <p class="alert alert-info">
                        This pet is displaying custom art!
                    </p>
                    @if(isset($pet->petArtist) && $pet->petArtist)
                        <h2 class="h5">Artist</h2>
                        <div class="col-md">
                            <p><strong>Artist:</strong> {!! $pet->petArtist !!}</p>
                        </div>
                    @else
                        No credits given.
                    @endif
                </div>
            @endif
            @if($pet->description)
                <hr>
                <h2 class="h5">Profile</h2>
                <div class="card-body parsed-text">
                    {!! $pet->description !!}
                </div>
            @endif
        </div>

        @if(Auth::check() && ($pet->user_id == Auth::user()->id || Auth::user()->hasPower('edit_inventories')))
            <ul class="list-group list-group-flush">
                @include('user._pet_form', ['pet' => $pet, 'user' => Auth::user()])
            </ul>
        @endif
    </div>
@endsection
