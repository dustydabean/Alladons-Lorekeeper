@extends('admin.layout')

@section('admin-title') Themes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/theme']) !!}

<h1>Themes</h1>

<p>You can create new Themes here for your users to be able to select from. </p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/themes/create') }}"><i class="fas fa-plus"></i> Create New Themes Post</a></div>
@if(!count($themes))
    <p>No themes found.</p>
@else
    {!! $themes->render() !!}
    <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-12 col-md-5 font-weight-bold">Name</div>
            <div class="col-6 col-md-3 font-weight-bold">Creators</div>
            <div class="col-6 col-md font-weight-bold">Last Edited</div>
        </div>
        @foreach($themes as $theme)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-12 col-md-5">
            {!! $theme->is_default ? '<i class="fas fa-star mr-2"></i>' : '' !!}
            {!! $theme->is_active ?:  '<i class="fas fa-eye-slash mr-2"></i>' !!}
            {{ $theme->name }}
        </div>
        <div class="col-3 col-md-3">{{ $theme->creator ? $theme->creatorName : 'N/A' }}</div>
        <div class="col-6 col-md-3">{!! $theme->updated_at->calendar() !!}</div>
        <div class="col-3 col-md-1 text-right"><a href="{{ url('admin/themes/edit/'.$theme->id) }}" class="btn btn-primary py-0 px-2">Edit</a></div>
        </div>
        @endforeach
    </div>
    {!! $themes->render() !!}

@endif

@endsection
