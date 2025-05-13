@extends('layouts.app')


@section('title') rulespage @endsection


@section('content')
{!! breadcrumbs(['rulespage' => 'rulespage']) !!}


<div class="site-page-content parsed-text">
    {!! $page->parsed_text !!}
</div>


@endsection