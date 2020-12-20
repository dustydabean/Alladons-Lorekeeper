@extends('home.layout')

@section('home-title') Foraging @endsection

@section('home-content')
{!! breadcrumbs(['Foraging' => 'foraging']) !!}

<h1>
    Foraging
</h1>

<p>Welcome to foraging! Here you can choose an area to check for goodies</p>

@if(!count($tables))
    <p>No active forages. Come back soon!</p>
@else
<div class="container text-center">
    @foreach($tables as $table)
        {!! Form::open(['url' => 'foraging/forage/'.$table->id ]) !!}
        {!! Form::submit('Forage in the ' . $table->display_name , ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    @endforeach
</div>
@endif
@endsection