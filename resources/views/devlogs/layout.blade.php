@extends('layouts.app')

@section('title')
    Site News :: @yield('devlogs-title')
@endsection

@section('sidebar')
    @include('devlogs._sidebar')
@endsection

@section('content')
    @yield('devlogs-content')
@endsection
