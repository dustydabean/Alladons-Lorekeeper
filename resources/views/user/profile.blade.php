@extends('user.layout', ['user' => isset($user) ? $user : null])

@section('profile-title')
    {{ $user->name }}'s Profile
@endsection

@section('meta-img')
    {{ $user->avatarUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url]) !!}

    @if (mb_strtolower($user->name) != mb_strtolower($name))
        <div class="alert alert-info">This user has changed their name to <strong>{{ $user->name }}</strong>.</div>
    @endif

    @if ($user->is_banned)
        <div class="alert alert-danger">This user has been banned.</div>
        
@if(isset($user->profile->parsed_text))
    <div class="card mb-3" style="clear:both;">
        <div class="card-body">
            {!! $user->profile->parsed_text !!}
        </div>
    </div>
@endif

<div class="card-deck mb-4 profile-assets" style="clear:both;">
    <div class="card profile-currencies profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Bank</h5>
            <div class="profile-assets-content">
                @foreach($user->getCurrencies(false) as $currency)
                    <div>{!! $currency->display($currency->quantity) !!}</div>
                @endforeach
            </div>
            <div class="text-right"><a href="{{ $user->url.'/bank' }}">View all...</a></div>
        </div>
    </div>
    <div class="card profile-inventory profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Inventory</h5>
            <div class="profile-assets-content">
                @if(count($items))
                    <div class="row">
                        @foreach($items as $item)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if($item->imageUrl)
                                    <img src="{{ $item->imageUrl }}" data-toggle="tooltip" title="{{ $item->name }}" alt="{{ $item->name }}"/>
                                @else
                                    <p>{{ $item->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No items owned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/inventory' }}">View all...</a></div>
        </div>
    </div>
</div>

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

<h2>
    <a href="{{ $user->url.'/characters' }}">Characters</a>
    @if(isset($sublists) && $sublists->count() > 0)
        @foreach($sublists as $sublist)
        / <a href="{{ $user->url.'/sublist/'.$sublist->key }}">{{ $sublist->name }}</a>
        @endforeach
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
