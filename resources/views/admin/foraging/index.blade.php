@extends('admin.layout')

@section('admin-title') Forages @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Forages' => 'admin/data/forages']) !!}

<h1>Forages</h1>

<p>Forages will roll a random reward from the contents of the table.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/forages/create') }}"><i class="fas fa-plus"></i> Create New Forage</a></div>
@if(!count($tables))
    <p>No Forages found.</p>
@else
    {!! $tables->render() !!}
      <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
          <div class="col-2 col-md-2 font-weight-bold">ID</div>
          <div class="col-2 col-md-4 font-weight-bold">Name</div>
          <div class="col-6 col-md-5 font-weight-bold">Display Name</div>
          <div class="col-6 col-md-5 font-weight-bold">Is Active?</div>
        </div>
        @foreach($tables as $table)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
          <div class="col-2 col-md-2 ">#{{ $table->id }}</div>
          <div class="col-2 col-md-4">{{ $table->name }}</div>
          <div class="col-3 col-md-5">{!! $table->display_name !!}</div>
          <div class="col-3 col-md-5">@if($table->is_active) Yes @else No @endif</div>
          <div class="col-3 col-md-1 text-right"><a href="{{ url('admin/data/forages/edit/'.$table->id) }}" class="btn btn-primary py-0 px-2">Edit</a></div>
        </div>
        @endforeach
      </div>
    {!! $tables->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $tables->total() }} result{{ $tables->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection
