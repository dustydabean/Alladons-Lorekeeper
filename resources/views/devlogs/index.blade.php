@extends('layouts.app')

@section('title') 
    Dev Log 
@endsection

@section('sidebar')
    @include('devlogs._sidebar')
@endsection

@section('content')
{!! breadcrumbs(['Dev Logs' => 'devlogs']) !!}
<h1>Dev Logs</h1>
@if(count($devLogses))
    {!! $devLogses->render() !!}
    @foreach($devLogses as $devLogs)
        @include('devlogs._devlogs', ['dev-logs' => $devLogs, 'page' => FALSE])
    @endforeach
    {!! $devLogses->render() !!}
@else
    <div>No development logs yet.</div>
@endif
@endsection