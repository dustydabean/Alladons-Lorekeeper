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
    <p>Last foraged {!! pretty_date($user->foraging->last_foraged_at) !!}</p> 
@endif

@php
    $now = Carbon\Carbon::now();

    $date = Carbon\Carbon::parse($user->foraging->distribute_at);

    $diff = $date->diffInMinutes($now);
@endphp

@if($diff > 0)
    {{ $diff }} minutes till you finish scavenging!
@else

@if($diff <= 0 && $user->foraged == 0)
    {!! Form::open(['url' => 'foraging/forage/claim' ]) !!}
    {!! Form::submit('Claim Reward' , ['class' => 'btn btn-primary m-2']) !!}
    {!! Form::close() !!}
@endif

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
@endif
@endsection