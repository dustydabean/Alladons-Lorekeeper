@extends('home.layout')

@section('home-title') Friends @endsection

@section('home-content')
{!! breadcrumbs(['Friends' => 'friends']) !!}

<h1>
    Friends
</h1>

<div class="text-right">
    <a href="{{ url('friends/requests') }}">
        <div class="btn btn-info">
            View Requests
        </div>
    </a>
</div>

<div class="row">
    @foreach($friends as $friend)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <img src="/images/avatars/{{ $friend->other(Auth::user()->id)->avatar }}"
                            style="width:125px; height:125px; float:left; border-radius:50%; margin-right:25px;"
                            alt="{{ $friend->other(Auth::user()->id)->name }}" >
                        {!! $friend->other(Auth::user()->id)->displayName !!}
                    </h5>
                    <p class="card-text">
                        <a href="{{ url($friend->other(Auth::user()->id)->url) }}" class="btn btn-primary">View Profile</a>
                        {{-- remove friend --}}
                        {!! Form::open(['url' => 'friends/remove/'.$friend->id]) !!}
                            {!! Form::button('Remove', ['class' => 'btn btn-danger', 'data-toggle' => 'tooltip', 'title' => 'Remove this friend.', 'type' => 'submit']) !!}
                        {!! Form::close() !!}
                    </p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection