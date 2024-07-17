@extends('user.layout', ['user' => isset($user) ? $user : null])

@section('profile-title')
    {{ $user->name }}'s Profile
@endsection

@section('meta-img')
    {{ $user->avatarUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url]) !!}

    <div class="card-deck mb-4 profile-assets" style="clear:both;">
    <div class="card profile-inventory profile-assets-card">
    <div class="card-body text-center">
            <h5 class="card-title">Completed Collections</h5>
            <div class="profile-assets-content">
                @if(count($collections))
                    <div class="row">
                        @foreach($collections as $collection)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if($collection->imageUrl)
                                    <img src="{{ $collection->imageUrl }}" data-toggle="tooltip" title="{{ $collection->name }}" alt="{{ $collection->name }}"/>
                                @else
                                    <p>{{ $collection->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No collections completed.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/collection-logs' }}">View all...</a></div>
        </div>
        </div>
        </div>

    @if (mb_strtolower($user->name) != mb_strtolower($name))
        <div class="alert alert-info">This user has changed their name to <strong>{{ $user->name }}</strong>.</div>
    @endif

    @if ($user->is_banned)
        <div class="alert alert-danger">This user has been banned.</div>
    @endif 

    @if ($user->is_deactivated)
        <div class="alert alert-info text-center">
            <h1>{!! $user->displayName !!}</h1>
            <p>This account is currently deactivated, be it by staff or the user's own action. All information herein is hidden until the account is reactivated.</p>
            @if (Auth::check() && Auth::user()->isStaff)
                <p class="mb-0">As you are staff, you can see the profile contents below and the sidebar contents.</p>
            @endif
        </div>
    @endif

    @if (!$user->is_deactivated || (Auth::check() && Auth::user()->isStaff))
        @include('user._profile_content', ['user' => $user, 'deactivated' => $user->is_deactivated])
    @endif

@endsection
