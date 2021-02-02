@extends('admin.layout')

@section('admin-title') Event Currency @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Event Currency' => 'admin/grants/event-currency']) !!}

<h1>
    Event Currency Info
    @if($currency->id)
        <a href="#" class="btn btn-outline-danger float-right clear-currency-button">Clear Event Currency</a>
    @endif
</h1>

<p>This page displays information about the current event currency and any global tracking. You may also clear all users' event points here.</p>

@if($currency->id)
    <p>The current event currency is {{ $currency->name }}.</p>

    <h3>Global Event Score</h3>
    @if(Settings::get('global_event_score'))
        <p>There <strong>is</strong> currently global tracking of event score. The current total is {!! $currency->display($total ? $total->quantity : 0) !!}.</p>

        <p>
            Users can view information about the current global score here:

            {!! Form::text('url', url('event-tracking'), ['class' => 'form-control mb-4', 'disabled']) !!}

            This page includes information from a text page.
            @if(Auth::user()->hasPower('edit_pages')) You can edit this page <a href="{{ url('/admin/pages/edit/'.$page->id) }}">here</a>.@endif
        </p>

        @if(Settings::get('global_event_goal') != 0)
            <h4>Goal</h4>

            <p>The current goal is set to {!! $currency->display(Settings::get('global_event_goal')) !!}. Note that no action is taken automatically as a result of user progress, so any rewards, etc. will need to be distributed through other means.</p>

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
        @else
            <p>There is no goal set.</p>
        @endif
    @else
        <p>There <strong>is not</strong> currently global tracking of event score.</p>
    @endif
@else
    <p>The set currency does not exist.</p>
@endif

<script>
    $(document).ready(function() {
        $('.clear-currency-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/grants/event-currency/clear') }}", 'Clear Event Currency');
        });
    });

</script>

@endsection
