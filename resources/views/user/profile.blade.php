@extends('user.layout', ['user' => isset($user) ? $user : null])

@section('profile-title') {{ $user->name }}'s Profile @endsection

@section('meta-img') {{ asset('/images/avatars/'.$user->avatar) }} @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url]) !!}


@if($user->is_banned)
    <div class="alert alert-danger">This user has been banned.</div>
@endif

@if($user->is_deactivated)
    <div class="alert alert-info text-center">
        <h1>{!! $user->displayName !!}</h1>
            <p>This account is currently deactivated, be it by staff or the user's own action. All information herein is hidden until the account is reactivated.</p>
        @if(Auth::check() && Auth::user()->isStaff)
            <p class="mb-0">As you are staff, you can see the profile contents below and the sidebar contents.</p>
        @endif
    </div>
@endif

@if(Auth::check() && $user->isBlocked(Auth::user()))
    <div class="alert alert-danger">You have blocked this user.</div>
    {!! Form::open(['url' => 'friends/block/'.$user->id]) !!}
        {!! Form::button('Unblock', ['class' => 'btn badge badge-danger mr-2 float-right', 'data-toggle' => 'tooltip', 'title' => 'Blocking this user will prevent them from viewing your profile or comments.', 'type' => 'submit']) !!}
    {!! Form::close() !!}
@elseif(Auth::check() && Auth::user()->isBlocked($user))
    <div class="alert alert-danger">This user has blocked you.</div>
    {!! Form::open(['url' => 'friends/block/'.$user->id]) !!}
        {!! Form::button('Block', ['class' => 'btn badge badge-danger mr-2 float-right', 'data-toggle' => 'tooltip', 'title' => 'Blocking this user will prevent them from viewing your profile or comments.', 'type' => 'submit']) !!}
    {!! Form::close() !!}
@elseif(!$user->is_deactivated || Auth::check() && Auth::user()->isStaff)
    @include('user._profile_content', ['user' => $user, 'deactivated' => $user->is_deactivated])
@endif

@endsection
@section('scripts')
<script>
        const id = '{{ $user->id }}';
        $('.add-friend').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('friends/requests') }}/" + id);
        });
</script>
@endsection