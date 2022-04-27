@extends('home.layout')

@section('home-title') Foraging @endsection

@section('home-content')
{!! breadcrumbs(['Foraging' => 'foraging']) !!}

<h1>
    Foraging
</h1>

<p>Welcome to foraging! Here you can choose an area to check for goodies.</p>
<p>Goods will be claimable after you return from scavenging! Usually, about an hour is the amount of time it takes to check out an area.</p>
@if($user->foraging->last_foraged_at)
    <p>
        Last Foraged: {!! pretty_date($user->foraging->last_foraged_at) !!}
    <br>
        Foraging Stamina Left: {{ $user->foraging->stamina }}
    </p> 
@endif

@php
    // getting a php static var for safari because it sucks
    $now = Carbon\Carbon::now();
    $diff = $now->diffInMinutes($user->foraging->distribute_at, false);
    $left = $now->diffInHours($user->foraging->reset_at, false);
@endphp

<script>
    // this is ugly up here and i hate it but it wont work otherwise
    let now = new Date("<?php echo date('Y-m-d H:i:s'); ?>");
    function timeCount(timer) {
        // timer = carbon time
        setInterval(function() { 
            var date = new Date(timer);
            getServerTime();
            // count down time difference between now and date
            var diff = date.getTime() - now.getTime();
            var time = new Date(diff);

            var seconds = time.getUTCSeconds();
            if(seconds < 10) seconds = "0" + seconds;

            var minutes = time.getUTCMinutes();
            if(minutes < 10) minutes = "0" + minutes;

            if(seconds == '00' && minutes == '00') {
                // reload page
                location.reload();
            }

            var text = "Foraging complete in " + minutes + ":" + seconds + "!";
            $("#time").text(text);
        }, 1000);
    }
    function getServerTime()
    {
        // ajax get call to get the time
        $.ajax({
            url: '{{ url("time") }}',
            type: 'GET',
            success: function(data) {
                // update the time
                now = new Date(data);
            }
        });
    }
</script>

@if($user->foraging->distribute_at && $user->foraging->distribute_at > $now)
    {{-- Whilst foraging is in progress--}}
    <script>
        // we have to check for safari since it doesn't agree with formatted times
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        var timeLeft = Date.parse("<?php echo $user->foraging->distribute_at ?>");
        // if not safari, set off the loop!
        if(!isSafari) setInterval(timeCount(timeLeft), 1000);
    </script>

    <div id="time">Foraging complete in {{ $diff < 1 ? 'less than a minute' : $diff }}</div>
    <p>Started {!! pretty_date($user->foraging->last_foraged_at)!!}
@elseif($user->foraging->distribute_at <= $now && $user->foraging->last_forage_id)
    {{-- When foraging is done and we can claim --}}
    <div class="container text-center">
        {!! Form::open(['url' => 'foraging/claim' ]) !!}
            <img src="{{ $user->foraging->forage->imageUrl }}" class="mb-2" style="max-width: 30%;"/>
            <br>
            {!! $user->foraging->forage->fancyDisplayName !!}
            <br>
            {!! Form::submit('Claim Reward' , ['class' => 'btn btn-primary m-2']) !!}
        {!! Form::close() !!}
    </div>
@elseif($user->foraging->stamina > 0)
    {{-- Base State --}}
    @if(!count($tables))
        <p>No active forages. Come back soon!</p>
    @else
    <div class="row text-center">
        @foreach($tables as $table)
            <div class="col-md-4">
                {!! Form::open(['url' => 'foraging/forage/'.$table->id ]) !!}

                    <img src="{{ $table->imageUrl }}" class="img-fluid mb-2"/>
                    {!! Form::submit('Forage in the ' . $table->display_name , ['class' => 'btn btn-primary m-2']) !!}

                {!! Form::close() !!}
            </div>
        @endforeach
    </div>
    @endif
@else
    <div class="alert alert-info">
        You've exhausted yourself today and have no stamina left. Come back tomorrow to continue foraging!
    </div>
@endif
@endsection