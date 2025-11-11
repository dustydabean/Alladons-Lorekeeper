@extends('layouts.app')


@section('title') Guides @endsection


@section('content')
{!! breadcrumbs(['Guides' => 'guides']) !!}


<div class="site-page-content parsed-text">
    {!! $page->parsed_text !!}
</div>


@endsection
