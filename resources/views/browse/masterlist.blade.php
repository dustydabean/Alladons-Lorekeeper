@extends('layouts.app')

@section('title')
    Character Masterlist
@endsection

@section('sidebar')
    @include('browse._sidebar')
@endsection

@section('content')
    {!! breadcrumbs(['Character Masterlist' => 'masterlist']) !!}
    <h1>Alladon Masterlist</h1>

    @include('browse._masterlist_content', ['characters' => $characters])
@endsection

@section('scripts')
    @include('browse._masterlist_js')
@endsection
