@extends('world.layout')

@section('title') Genetics @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Genetics' => 'world/genetics']) !!}
<h1>Genetics</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('variant', $options, Request::get('variant'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $genetics->render() !!}
@foreach($genetics as $loci)
    <div class="card mb-3">
        <div class="card-body">
        @include('world._gene_entry', ['loci' => $loci])
        </div>
    </div>
@endforeach
{!! $genetics->render() !!}

<div class="text-center mt-4 small text-muted">{{ $genetics->total() }} result{{ $genetics->total() == 1 ? '' : 's' }} found.</div>

@endsection
