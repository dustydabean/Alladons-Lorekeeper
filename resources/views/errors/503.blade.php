@extends('layouts.app')

@section('title') Down for Maintenance @endsection

@section('content')
    <h1 class="text-center">{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }}</h1>
    <h4 class="text-center font-italic">is currently down for maintenance</h4>
    <p class="text-center">We'll be back shortly! Please see our <a href="{{ url('logs') }}">development logs</a> for more information.</p>
@endsection