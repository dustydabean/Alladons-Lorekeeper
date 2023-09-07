@extends('dailies.layout')

@section('dailies-title') {{ $daily->name }} @endsection

@section('dailies-content')
{!! breadcrumbs([ucfirst(__('dailies.dailies')) => ucfirst(__('dailies.dailies')), $daily->name => $daily->url]) !!}

<h1>
    {{ $daily->name }}
</h1>


<div class="text-center">
    @if($daily->has_image)<img src="{{ $daily->dailyImageUrl }}" style="max-width:100%" alt="{{ $daily->name }}" />@endif
    <p>{!! $daily->parsed_description !!}</p>
</div>

@if($daily->has_button_image)
<div class="row justify-content-center mt-5">
    <form action="" method="post">
        @csrf
        <button class="btn" style="background-color:transparent;" name="daily_id" value="{{ $daily->id }}">
           <img src="{{ $daily->buttonImageUrl }}" class="w-100" style="max-width:200px;"/>
        </button>
    </form>
</div>
@else
<div class="row justify-content-center mt-5">
    <form action="" method="post">
        @csrf
        <button class="btn btn-primary" name="daily_id" value="{{ $daily->id }}">Collect Reward!</button>
    </form>
</div>
@endif

@endsection

@section('scripts')
<script>


</script>
@endsection
