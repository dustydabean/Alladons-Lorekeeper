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
    <table class="table table-sm">
                <thead class="thead-light">
                        <tr class="d-flex">
                            <th class="col-2">ID</th>
                            <th class="col-2">Name</th>
                            <th class="col-2">Display Name</th>
                            <th class="col-2">Is Active?</th>
                            <th class="col-4"></th>
                        </tr>
                </thead>
                <tbody>
                    @foreach($tables as $table)
                      <tr class="d-flex">
                        <td class="col-2">#{{ $table->id }}</td>
                        <td class="col-2">{{ $table->name }}</td>
                        <td class="col-2">{!! $table->display_name !!}</td>
                        <td class="col-2">@if($table->is_active) Yes @else No @endif</td>
                        <td class="col-2"><a href="{{ url('admin/data/forages/edit/'.$table->id) }}" class="btn btn-primary py-0 px-2">Edit</a></td>
                      </tr>
                    @endforeach
                </tbody>
            </table>
    {!! $tables->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $tables->total() }} result{{ $tables->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection
