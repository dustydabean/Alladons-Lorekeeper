@extends('admin.layout')

@section('admin-title') Themes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Theme Editor' => 'admin/theme-editor']) !!}

<h1>Theme Editor</h1>

<p>Here you can edit and create site themes by changing simple things such as the background/header images and menu colors.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/theme-editor/create') }}"><i class="fas fa-plus"></i> Create New Theme</a></div>
@if(!count($themes))
    <p>No themes found.</p>
@else
    {!! $themes->render() !!}
      <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
          <div class="col-12 col-md-5 font-weight-bold">Name</div>
        </div>
        @foreach($themes as $theme)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
          <div class="col-12 col-md-10"><a href="{{ $theme->url }}">{{ $theme->name }}</a></div>
          <div class="col-3 col-md-1 text-right"><a href="{{ url('admin/theme-editor/edit/'.$theme->id) }}" class="btn btn-primary py-0 px-2">Edit</a></div>
        </div>
        @endforeach
      </div>
    {!! $themes->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $themes->total() }} result{{ $themes->total() == 1 ? '' : 's' }} found.</div>

@endif

@endsection
