@extends('layouts.app')

@section('title') Event Tracking @endsection

@section('content')
{!! breadcrumbs(['Event Tracking' => '/event-tracking']) !!}
<h1>Event Tracking</h1>

<div class="site-page-content parsed-text">
    {!! $page->parsed_text !!}
</div>

@if($currency->id)
    @if(Settings::get('global_event_goal') != 0)

        <div class="progress mb-2" style="height: 2em;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="{{ Settings::get('global_event_goal') }}">
                @if($total && $total->quantity > 0)
                    <h5 class="align-self-center my-2">{{ $total ? $total->quantity : 0 }}/{{ Settings::get('global_event_goal') }}</h5>
                @endif
            </div>
        </div>

        <!--
            Inverse progress bar

            <div class="progress mb-2" style="height: 2em;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $inverseProgress }}%" aria-valuenow="{{ $inverseProgress }}" aria-valuemin="0" aria-valuemax="{{ Settings::get('global_event_goal') }}">
                    @if($total && (Settings::get('global_event_goal') - $total->quantity) > 0)
                        <h5 class="align-self-center my-2">{{ $total ? Settings::get('global_event_goal') - $total->quantity : Settings::get('global_event_goal') }}/{{ Settings::get('global_event_goal') }}</h5>
                    @endif
                </div>
            </div>

        -->

        <p class="text-center">
            The current event currency is {{ $currency->name }}, and the current global total is {!! $currency->display($total ? $total->quantity : 0) !!}
            @if(Settings::get('global_event_goal') != 0)
                . The current overall goal is {!! $currency->display(Settings::get('global_event_goal')) !!}!
            @else
                !
            @endif
        </p>
    @endif
@else
    <p>Oh no! It seems there's no event currency.</p>
@endif

@endsection
