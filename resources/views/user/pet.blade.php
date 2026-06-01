@php $namespace = (Request::url() == url('pets/view/'.$pet->id)) && (Auth::check() && Auth::user()->id == $pet->user_id); @endphp
@extends(($namespace ? 'home' : 'user') . '.layout')

@section($namespace ? 'title' : 'profile-title')
    {{ $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name }}
@endsection

@section($namespace ? 'content' : 'profile-content')
    {!! $namespace
        ? breadcrumbs(['Companions' => 'pets', $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name => $pet->url])
        : breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Companions' => $user->url . '/pets', $pet->pet_name ? $pet->pet_name . ' (' . $pet->pet->name . ')' : $user->name . "'s " . $pet->pet->name => $pet->url]) !!}

    <h1 class="mb-0">
        {!! $pet->pet_name
            ? $pet->pet_name . ' (' . $user->displayName . "'s " . $pet->pet->displayName . ')'
            : $user->name . "'s " . $pet->pet->displayName !!}
    </h1>
    <div>
        <span class="badge badge-primary">ID #{{ $pet->id }}</span>
    </div>

    @if (!$namespace)
        <div class="container justify-content-right text-right my-3">
            <a href="{{ $user->url . '/pets' }}">
                <div class="btn btn-primary">Return to Companions</div>
            </a>
        </div>
    @endif

    @if (Auth::check() && ($pet->user_id !== Auth::user()->id && Auth::user()->hasPower('edit_inventories')))
        <div class="alert alert-warning">
            You are editing this companion as a staff member.
        </div>
    @endif

    <div class="row world-entry align-items-center">
        <div class="col-md-3 world-entry-image">
            <img class="img-fluid rounded mb-2" src="{{ $pet->pet->image($pet->id) }}" data-toggle="tooltip" title="{{ $pet->pet_name ?? $pet->pet->name }}" alt="{{ $pet->pet_name ?? $pet->pet->name }}" />
            <div class="mb-2 mb-md-0">
                <h5 class="mb-0">
                    Level {{ $pet->level->levelName ?? 1 }}
                </h5>
                @if ($pet->level && $pet->level->levelName < Settings::get('max_pet_level'))
                    <div class="small">
                        Will level up {!! pretty_date($pet->level->levelsAt) !!}.
                    </div>
                    <div class="small" style="opacity: 0.65;">
                        (<b>{{ $pet->level->bonding }} EXP</b>, minus {{ $pet->level->bonding > 0 ? $pet->level->bonding * 7 : 0 }} days)
                    </div>
                @else
                    <div class="small" style="opacity: 0.65;">
                        (Max Level)
                    </div>
                @endif
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
                        {{-- @if ($namespace)
                            @if (Auth::check() && Auth::user()->id == $pet->character->user_id && $pet->canBond())
                                <div class="form-group mb-0">
                                    {!! Form::open(['url' => 'pets/bond/' . $pet->id]) !!}
                                    {!! Form::submit('Bond', ['class' => 'btn btn-primary']) !!}
                                    {!! Form::close() !!}
                                </div>
                            @else
                                <div class="alert alert-warning mb-0">{{ $pet->canBond(true) }}</div>
                            @endif
                        @endif --}}
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
                    This companion is displaying custom art!
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

    @php
        $logs = \App\Models\Pet\PetLog::where('stack_id', $pet->id)
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();
    @endphp
    @if ($logs->count())
        <div class="card mt-3">
            <div class="card-header h5 mb-0">Recent Activity</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Log</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{!! $log->log !!}</td>
                                <td>{!! format_date($log->created_at) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
@endsection