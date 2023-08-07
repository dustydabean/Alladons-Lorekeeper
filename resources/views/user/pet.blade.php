@php $namespace = (Request::url() == url('pets/view/'.$pet->id)) && (Auth::check() && Auth::user()->id == $pet->user_id); @endphp
@extends(($namespace ? 'home' : 'user').'.layout')

@section($namespace ? 'title' : 'profile-title')
    {{ $pet->pet_name ? $pet->pet_name . ' ('.$pet->pet->name.')' : $user->name."'s ".$pet->pet->name }}
@endsection

@section($namespace ? 'content' : 'profile-content')

    {!! $namespace ?
        breadcrumbs(['Pets' => 'pets', $pet->pet_name ? $pet->pet_name . ' ('.$pet->pet->name.')' : $user->name."'s ".$pet->pet->name => $pet->url]) :
        breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Pets' => $user->url . '/pets',
        $pet->pet_name ? $pet->pet_name . ' ('.$pet->pet->name.')' : $user->name."'s ".$pet->pet->name => $pet->url])
    !!}

    <h1>
        {{ $pet->pet_name ? $pet->pet_name . ' ('. $user->name."'s ". $pet->pet->name.')' : $user->name."'s ".$pet->pet->name }}
        <a href="{{ $pet->pet->url }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>
    </h1>

    @if(!$namespace)
        <div class="container justify-content-right text-right m-3">
            <a href="{{ $user->url.'/pets' }}">
                <div class="btn btn-primary">Return to Pets</div>
            </a>
        </div>
    @endif

    @if(Auth::check() && ($pet->user_id !== Auth::user()->id && Auth::user()->hasPower('edit_inventories')))
        <div class="alert alert-warning">
            You are editing this pet as a staff member.
        </div>
    @endif

    <div class="row world-entry">
        <div class="col-md-3 world-entry-image">
            <img class="img-fluid" src="{{ $pet->pet->VariantImage($pet->id) }}" data-toggle="tooltip" title="{{ $pet->pet_name ?? $pet->pet->name }}" alt="{{ $pet->pet_name ?? $pet->pet->name }}" />
        </div>
        <div class="col-md-9">
            <div class="row col-12 world-entry-text">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    @include('user._pet_drops', ['pet' => $pet, 'drops' => $pet->drops])
                </div>
            </div>
        </div>
    </div>
    @if (Auth::check() && ($pet->user_id == Auth::user()->id || Auth::user()->hasPower('edit_inventories')))
        <div class="card">
            <ul class="list-group list-group-flush">
                @include('user._pet_form', ['pet' => $pet, 'user' => Auth::user()])
            </ul>
        </div>
    @endif
@endsection
