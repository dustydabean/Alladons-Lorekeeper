@extends('home.layout')

@section('home-title') Foraging @endsection

@section('home-content')
{!! breadcrumbs(['Foraging' => 'foraging']) !!}

<h1>
    Foraging
</h1>

<p>Welcome to foraging! Here you can choose an area to check for goodies.</p>
<p>Goods will be claimable after you return from scavenging! Usually, about
    {{-- convert integer to minutes using carbon (multiple integer by 60) --}}
    {{ Config::get('lorekeeper.foraging.forage_time') . ' minute' . (Config::get('lorekeeper.foraging.forage_time') > 1 ? 's' : '')}}
    is the amount of time it takes to check out an area.</p>
<div class="row">
    <div class="col-md-6">
        @if($user->foraging->foraged_at)
            <p>
                Last Foraged: {!! pretty_date($user->foraging->foraged_at) !!}
            <br>
                Foraging Stamina Left: {{ $user->foraging->stamina }}
            </p>
        @endif
    </div>
    @if(Config::get('lorekeeper.foraging.use_characters') && !$user->foraging->distribute_at)
        <div class="col-md-6 justify-content-center text-center">
            <h3>Current Character</h3>
            @if (!$user->foraging->character)
                <p>No character selected!</p>
            @else
                <div>
                    <a href="{{ $user->foraging->character->url }}">
                        <img src="{{ $user->foraging->character->image->thumbnailUrl }}" style="width: 150px;" class="img-thumbnail" />
                    </a>
                </div>
                <div class="mt-1">
                    <a href="{{ $user->foraging->character->url }}" class="h5 mb-0">
                        @if (!$user->foraging->character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif {{ $user->foraging->character->fullName }}
                    </a>
                </div>
            @endif
            {!! Form::open(['url' => 'foraging/edit/character']) !!}
                {!! Form::select('character_id', $characters, $user->foraging->character_id, ['class' => 'form-control m-1', 'placeholder' => 'None Selected']) !!}
                {!! Form::submit('Select Character', ['class' => 'btn btn-primary mb-2']) !!}
            {!! Form::close() !!}
        </div>
    @endif
</div>

<hr class="w-50 ml-auto mr-auto" />

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

            var hours = now.getUTCHours();

            if((seconds == '00' && minutes == '00' && hours >= date.getUTCHours()) || hours > date.getUTCHours()) {
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
    <div class="container text-center">
        @if (Config::get('lorekeeper.foraging.use_characters') && $user->foraging->character)
            <div class="mb-1">
                <a href="{{ $user->foraging->character->url }}">
                    <img src="{{ $user->foraging->character->image->thumbnailUrl }}" style="width: 150px;" class="img-thumbnail" />
                </a>
            </div>
        @endif
        <div id="time">Foraging complete in {{ $diff < 1 ? 'less than a minute' : $diff }}</div>
        <p>Started {!! pretty_date($user->foraging->foraged_at)!!}
    </div>
@elseif($user->foraging->distribute_at <= $now && $user->foraging->forage_id)
    {{-- When foraging is done and we can claim --}}
    <div class="container text-center">
        @if (Config::get('lorekeeper.foraging.use_characters') && $user->foraging->character)
            <div class="mb-1">
                <a href="{{ $user->foraging->character->url }}">
                    <img src="{{ $user->foraging->character->image->thumbnailUrl }}" style="width: 150px;" class="img-thumbnail" />
                </a>
            </div>
        @endif
        {!! Form::open(['url' => 'foraging/claim' ]) !!}
            @if($user->foraging->forage->imageUr)
                <img src="{{ $user->foraging->forage->imageUrl }}" class="mb-2" style="max-width: 30%;"/>
                <br>
            @endif
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
        @foreach($tables->sortByDesc('is_visible') as $table)
            <div class="col-md-4">
                {!! Form::open(['url' => 'foraging/forage/'.$table->id ]) !!}

                    <div><img src="{{ $table->imageUrl }}" class="img-fluid mb-2"/></div>
                    <div>{!! Form::button(($table->isVisible ? '' : '<i class="fas fa-crown"></i> ') . 'Forage in the ' . $table->display_name , ['class' => 'btn btn-primary m-2', 'type' => 'submit']) !!}</div>

                        <div class="alert alert-info pb-0">
                            <ul style="list-style: none;">
                                <li>This forage costs {{$table->stamina_cost}} stamina.</li>
                                @if($table->has_cost)
                                    <li>This forage costs {!! $table->currency->display($table->currency_quantity) !!}.</li>
                                @endif
                            </ul>
                        </div>
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
