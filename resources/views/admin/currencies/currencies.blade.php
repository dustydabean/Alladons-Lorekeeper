@extends('admin.layout')

@section('admin-title') Currencies @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Currencies' => 'admin/data/currencies']) !!}

<h1>Currencies</h1>

<p>This is a list of currencies that can be earned by users and/or characters. While they're collectively called "currencies", they can be used to track activity counts, event-only reward points, etc. and are not necessarily transferrable and/or can be spent. More information can be found on the creating/editing pages.</p>

<p>The order of currencies as displayed on user and character profiles can be edited from the <strong><a href="{{ url('admin/data/currencies/sort') }}">Sort Currencies</a></strong> page.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/currencies/create') }}"><i class="fas fa-plus"></i> Create New Currency</a></div>
{!! $currencies->render() !!}
  <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
      <div class="col-12 col-md-4 font-weight-bold">Name</div>
      <div class="col-4 col-md-4 font-weight-bold">Displays As</div>
      <div class="col-4 col-md-3 font-weight-bold">Attaches To</div>
    </div>
    @foreach($currencies as $currency)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-4 ">{{ $currency->name }} @if($currency->abbreviation) ({{ $currency->abbreviation }}) @endif</div>
      <div class="col-4 col-md-4">{!! $currency->display(100) !!}</div>
      <div class="col-4 col-md-3">{{ $currency->is_user_owned ? 'User' : '' }} {{ ($currency->is_character_owned && $currency->is_user_owned) ? '+' : '' }} {{ $currency->is_character_owned ? 'Character' : '' }}</div>
      <div class="col-4 col-md-1"><a href="{{ url('admin/data/currencies/edit/'.$currency->id) }}" class="btn btn-primary">Edit</a></div>
    </div>
    @endforeach
  </div>
{!! $currencies->render() !!}
<div class="text-center mt-4 small text-muted">{{ $currencies->total() }} result{{ $currencies->total() == 1 ? '' : 's' }} found.</div>
@endsection
