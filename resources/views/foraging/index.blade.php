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
    {!! $tables->render() !!}
        @foreach($tables as $table)

            <div class="btn btn-primary">{{ $table->displayName}}</div>
        @endforeach
      </div>
    {!! $tables->render() !!}
@endif
@endsection