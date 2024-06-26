@extends('layouts.app')

@section('title') 
    Shops :: 
    @yield('activities-title')
@endsection

@section('content')
    @yield('activities-content')
@endsection

@section('scripts')
@parent
@endsection