@extends('layouts.app')

@section('title') Team @endsection

@section('content')
{!! breadcrumbs(['Team' => 'team']) !!}

<h1>{{ config('lorekeeper.settings.site_name') }} Team</h1>

<hr>
@foreach($staff as $rankId=>$staffRanks)
	<h3 class="my-3 text-center" style="color: #{{ $ranks[$rankId]->color }}">
	    <i class="{{ $ranks[$rankId]->icon }}"></i> {{ $ranks[$rankId]->name }}
	</h3>
	<p class="text-center">{!! $ranks[$rankId]->parsed_description !!}</p>

	@foreach($staffRanks->chunk(4) as $chunk)
	<div class="row justify-content-center">
		@foreach($chunk as $user)
			<div class="col-12 col-md-6 mb-3">
        		@include('browse._staff_listing_content')
			</div>
		@endforeach
	</div>
@endforeach
	<hr>

@endforeach

@endsection