@extends('home.layout')

@section('home-title') Foraging @endsection

@section('home-content')
{!! breadcrumbs(['Foraging' => 'foraging']) !!}

<h1>
    Foraging
</h1>

<p>Welcome to foraging! Here you can choose an area to check for goodies.</p>
<p>Goods will be claimable after you return from scavenging! Usually, about an hour is the amount of time it takes to check out an area.</p>
@if($user->foraging->last_foraged_at !=NULL)
    <p>Last foraged {!! pretty_date($user->foraging->last_foraged_at) !!} <br>
        <small class="text-muted">Please note that the forage timer is reset 24 hours after you last began to forage. You may only forage once every 24 hours!</small>
    </p> 
@endif

@php
    $now = Carbon\Carbon::now();
    $diff = $now->diffInMinutes($user->foraging->distribute_at, false);
    $left = $now->diffInHours($user->foraging->reset_at, false);
@endphp

@if($user->foraging->distribute_at >= $now)
    <div >@if($diff >= 0 && $diff < 1) 1> minute till you finish foraging! @else {{ $diff }} minutes till you finish foraging!@endif</div>
    <p>Started {!! pretty_date($user->foraging->last_foraged_at)!!}
@else
    @if($user->foraging->distribute_at <= $now && $user->foraging->last_forage_id !== NULL)
        {!! Form::open(['url' => 'foraging/claim' ]) !!}
        {!! Form::submit('Claim Reward' , ['class' => 'btn btn-primary m-2']) !!}
        {!! Form::close() !!}
    @endif

    @if($user->foraging->reset_at <= $now && $user->foraging->distribute_at == NULL && $user->foraging->forage_id == NULL)

        @if(!count($tables))
            <p>No active forages. Come back soon!</p>
        @else
        <div class="container text-center">
            <div class="row text-center">
                @foreach($tables as $table)
                    {!! Form::open(['url' => 'foraging/forage/'.$table->id ]) !!}
                    {!! Form::submit('Forage in the ' . $table->display_name , ['class' => 'btn btn-primary m-2']) !!}
                    {!! Form::close() !!}
                @endforeach
            </div>
        </div>
        @endif
    @elseif($user->foraging->reset_at >= $now && $user->foraging->distribute_at == NULL && $user->foraging->forage_id == NULL)
        <p>You have already foraged today! Come back again in {!! pretty_date($user->foraging->reset_at) !!}.</p>
    @endif
@endif
@endsection