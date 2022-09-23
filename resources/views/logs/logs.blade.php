@extends('layouts.app')

@section('title') {{ $devLogs->title }} @endsection

@section('content')
    {!! breadcrumbs(['Site News' => 'news', 'Dev Logs' => 'logs', $devLogs->title => $devLogs->url]) !!}
    @include('logs._logs', ['logs' => $devLogs, 'page' => TRUE])
<hr>
<br><br>

@comments(['model' => $devLogs,
        'perPage' => 5
    ])

@endsection