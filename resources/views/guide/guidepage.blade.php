@extends('layouts.app')


@section('title') Guide @endsection


@section('content')
{!! breadcrumbs(['Guide' => 'guide']) !!}


<div class="site-page-content parsed-text">
    {!! $page->parsed_text !!}
</div>


@endsection
