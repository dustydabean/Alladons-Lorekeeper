@extends('admin.layout')

@section('admin-title') Trait Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/genetics/genes', 'Breeding Logs' => 'admin/genetics/logs']) !!}

<h1>
    Breeding Logs
</h1>

<p>List of breeding logs generated from the roller.</p>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('type', ['desc' => "Newest First", 'asc' => "Oldest First"], Request::get('type'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($logs))
    <p class="text-center">No breeding logs were found.</p>
@else
    {!! $logs->render() !!}
    <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-7 col-sm-4 font-weight-bold">Litter Name</div>
            <div class="col-5 col-sm-2 font-weight-bold">Children</div>
            <div class="col-7 col-sm-3 font-weight-bold">Rolled By</div>
            <div class="col-5 col-sm-3 font-weight-bold">Rolled</div>
        </div>
        @foreach($logs as $log)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-7 col-sm-4"><a href="{{ url('admin/genetics/logs/breeding/'.$log->id) }}">{{ $log->name }}</a></div>
                <div class="col-5 col-sm-2">{{ count($log->children) }} MYOs</div>
                <div class="col-7 col-sm-3">{!! $log->user ? $log->user->displayName : "Unknown" !!}</div>
                <div class="col-5 col-sm-3">
                    {!! pretty_date($log->rolled_at) !!}
                </div>
            </div>
        @endforeach
    </div>
    {!! $logs->render() !!}
@endif
@endsection

@section('scripts')
@parent
@endsection
