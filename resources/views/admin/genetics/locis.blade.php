@extends('admin.layout')

@section('admin-title') Trait Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/genetics']) !!}

<h1>
    Gene Groups
    <div class="float-right">
        <a class="btn btn-primary" href="{{ url('admin/genetics/create') }}"><i class="fas fa-plus mr-1"></i> New</a>
        <a class="btn btn-primary ml-1" href="{{ url('admin/genetics/sort') }}"><i class="fas fa-bars mr-1"></i> Sort</a>
    </div>
</h1>

<p>This is a list of gene groups (loci) that will be used to create and sort assignable genetics. The sorting order reflects the order in which the gene categories will be displayed in the inventory, as well as on the world pages.</p>


<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('type', ["Any Type", "Standard", "Gradient", "Numeric"], Request::get('type'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($locis))
    <p class="text-center">No gene groups found.</p>
@else
    {!! $locis->render() !!}
    <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-5 font-weight-bold">Name</div>
            <div class="col-7 font-weight-bold">Type</div>
        </div>
        @foreach($locis as $feature)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-5">{{ $feature->name }}</div>
                <div class="col-5">{{ ucfirst($feature->type) }} ({{ $feature->length }})</div>
                <div class="col-2">
                    <a href="{{ url('admin/genetics/edit/'.$feature->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a>
                </div>
            </div>
        @endforeach
    </div>
    {!! $locis->render() !!}
@endif
@endsection

@section('scripts')
@parent
@endsection
