@extends('devlogs.layout')

@section('title') 
    {{ $devLogs->title }}
@endsection

@section('devlogs-content')
    {!! breadcrumbs(['Dev Logs' => 'devlogs', $devLogs->title => $devLogs->url]) !!}
    @include('devlogs._devlogs', ['devlogs' => $devLogs, 'page' => TRUE])
<hr>
<br><br>

@comments(['model' => $devLogs,
        'perPage' => 5
    ])

@endsection