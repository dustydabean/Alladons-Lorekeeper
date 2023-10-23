@extends('dailies.layout')

@section('dailies-title') {{ $daily->name }} @endsection

@section('dailies-content')
{!! breadcrumbs([ucfirst(__('dailies.dailies')) => __('dailies.dailies'), $daily->name => $daily->url]) !!}

<h1>
    {{ $daily->name }}
</h1>


<div class="text-center">

    @if($daily->has_image)
    <img src="{{ $daily->dailyImageUrl }}" style="max-width:100%" alt="{{ $daily->name }}" />
    @endif
    <p>{!! $daily->parsed_description !!}</p>
</div>


<div class="text-center">
    <hr>
    <small>
        @if($daily->daily_timeframe == 'lifetime')
        You will be able to collect rewards once.
        @else
        You will be able to collect rewards {!! $daily->daily_timeframe !!}.
        @endif
        @if(Auth::check() && isset($cooldown))
        You can collect rewards {!! pretty_date($cooldown) !!}!
        @endif
    </small>
</div>

@if(Auth::user())
    @if($daily->type == 'Wheel' && $daily->wheel)
            @include('dailies._wheel_daily', ['wheel' => $daily->wheel])
        @endif
    @if($daily->type == 'Button')
        @include('dailies._button_daily')
    @endif
@else
<div class="row mt-2 mb-2 justify-content-center">
    <div class="alert alert-danger" role="alert">
        You must be logged in to collect {{ __('dailies.dailies') }}!
    </div>
</div>
@endif


@endsection

