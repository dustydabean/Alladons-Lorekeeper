@extends('dailies.layout')

@section('dailies-title') {{ $daily->name }} @endsection

@section('dailies-content')
{!! breadcrumbs([ucfirst(__('dailies.dailies')) => __('dailies.dailies'), $daily->name => $daily->url]) !!}

<h1>
    {{ $daily->name }}
</h1>


@if($daily->type == 'Wheel' && $daily->wheel)
@include('dailies._wheel_daily', ['wheel' => $daily->wheel])
@endif
@if($daily->type == 'Button')
@include('dailies._button_daily')
@endif




@endsection