@php $namespace = (Request::url() == url('pets/view/'.$pet->id)) && (Auth::check() && Auth::user()->id == $pet->user_id); @endphp
@extends(($namespace ? 'home' : 'user') . '.layout')

@section($namespace ? 'title' : 'profile-title')
    {{ $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name }}
@endsection

@section($namespace ? 'content' : 'profile-content')

    {!! $namespace
        ? breadcrumbs(['Pets' => 'pets', $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name => $pet->url])
        : breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Pets' => $user->url . '/pets', $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name => $pet->url]) !!}

    <h1>
        {!! $pet->pet_name
            ? $pet->pet_name . ' (' . $user->displayName . "'s " . ($pet->variant_id ? $pet->variant->variant_name . ' ' : '') . $pet->pet->displayName . ')'
            : $user->name . "'s " . ($pet->variant_id ? $pet->variant->variant_name . ' ' : '') . $pet->pet->displayName !!}
    </h1>

    @if (!$namespace)
        <div class="container justify-content-right text-right my-3">
            <a href="{{ $user->url . '/pets' }}">
                <div class="btn btn-primary">Return to Pets</div>
            </a>
        </div>
    @endif

    @if (Auth::check() && ($pet->user_id !== Auth::user()->id && Auth::user()->hasPower('edit_inventories')))
        <div class="alert alert-warning">
            You are editing this pet as a staff member.
        </div>
    @endif

    <div class="row world-entry">
        <div class="col-md-3 world-entry-image">
            <img class="img-fluid rounded mb-2" src="{{ $pet->pet->VariantImage($pet->id) }}" data-toggle="tooltip" title="{{ $pet->pet_name ?? $pet->pet->name }}" alt="{{ $pet->pet_name ?? $pet->pet->name }}" />
            <div class="mb-2 mb-md-0">
                <h5 class="mb-0">
                    Level {{ $pet->level->levelName ?? 0 }}
                </h5>
                <div class="small">
                    Will level up {!! pretty_date($pet->level->levelsAt) !!}.
                </div>
                <div class="small" style="opacity: 0.65;">
                    (<b>{{ $pet->level->bonding }} EXP</b>, minus {{ ($pet->level->bonding > 0) ? ($pet->level->bonding * 7) : 0 }} days)
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row col-12 world-entry-text">
                <div class="col-md-4 mb-2 text-center">
                    @if ($pet->character)
                        <h2 class="h5">Attached to {{ $pet->character->fullName }}</h2>
                        <a href="{{ $pet->character->url }}">
                            <img src="{{ $pet->character->image->thumbnailUrl }}" class="rounded img-thumbnail mb-2" alt="Thumbnail for {{ $pet->character->fullName }}" />
                        </a>
                    @endif
                    @if ($pet->evolution)
                        <h2 class="h5">Evolved</h2>
                        <p>
                            {{ $pet->evolution->evolution_name }} (Stage {{ $pet->evolution->evolution_stage }})
                        </p>
                    @endif
                </div>
                @if ($pet->pet->hasDrops)
                    <div class="col-md-8 mb-2">
                        @include('user._pet_drops', ['pet' => $pet, 'drops' => $pet->drops])
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="pl-2 pr-2 pb-2">
        @if ($pet->has_image)
            <div>
                <p class="alert alert-info">
                    This pet is displaying custom art!
                    @if (isset($pet->petArtist) && $pet->petArtist)
                        <b>Artist:</b> {!! $pet->petArtist !!}
                    @else
                        No credits given.
                    @endif
                </p>

            </div>
        @endif
        @if ($pet->description)
            <hr>
            <div>
                <h2 class="h5">Profile</h2>
                {!! $pet->description !!}
            </div>
        @endif
    </div>
    @if (Auth::check() && ($pet->user_id == Auth::user()->id || Auth::user()->hasPower('edit_inventories')))
        <div class="card">
            <ul class="list-group list-group-flush">
                @include('home._pet_form', ['pet' => $pet, 'user' => Auth::user()])
            </ul>
        </div>
    @endif
@endsection
