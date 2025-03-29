@extends('admin.layout')

@section('admin-title') Event Settings @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Event Settings' => 'admin/grants/event-settings']) !!}

<h1>
    Event Settings
    @if($currency->id)
        <a href="#" class="btn btn-outline-danger float-right clear-currency-button">Clear Event Currency</a>
    @endif
</h1>

<p>This page displays information about the current event currency and any global tracking. You may also clear all users' event score here.</p>

@if($currency->id)
    <p>The current event currency is {{ $currency->name }}.</p>

    <h3>Global Event Score</h3>
    @if(Settings::get('event_global_score'))
        <p>There <strong>is</strong> currently global tracking of event score. The current total is {!! $currency->display($total ? $total->quantity : 0) !!}.</p>

        <p>
            Users can view information about the current global score here:

            {!! Form::text('url', url('event-tracking'), ['class' => 'form-control mb-4', 'disabled']) !!}

            This page includes information from a text page.
            @if(Auth::user()->hasPower('edit_pages')) You can edit this page <a href="{{ url('/admin/pages/edit/'.$page->id) }}">here</a>.@endif
        </p>

        @if(Settings::get('event_global_goal') != 0)
            <h4>Goal</h4>

            <p>The current goal is set to {!! $currency->display(Settings::get('event_global_goal')) !!}. Note that no action is taken automatically as a result of user progress, so any rewards, etc. will need to be distributed through other means.</p>

            <div class="progress mb-2" style="height: 2em;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="{{ Settings::get('event_global_goal') }}">
                    @if($total && $total->quantity > 0)
                        <h5 class="align-self-center my-2">{{ $total ? $total->quantity : 0 }}/{{ Settings::get('event_global_goal') }}</h5>
                    @endif
                </div>
            </div>

            <!--
                Inverse progress bar

                <div class="progress mb-2" style="height: 2em;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $inverseProgress }}%" aria-valuenow="{{ $inverseProgress }}" aria-valuemin="0" aria-valuemax="{{ Settings::get('event_global_goal') }}">
                        @if($total && (Settings::get('event_global_goal') - $total->quantity) > 0)
                            <h5 class="align-self-center my-2">{{ $total ? Settings::get('event_global_goal') - $total->quantity : Settings::get('event_global_goal') }}/{{ Settings::get('event_global_goal') }}</h5>
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

<h2 class="mt-4">Team Settings</h2>

<p>Teams are currently <strong>{{ Settings::get('event_teams') ? 'enabled' : 'disabled' }}</strong>.

<p>
    Here you can set teams that users may join as part of an event. Note that teams <strong>must be enabled</strong> for this to have any effect, but you may still adjust these settings, e.g. to perform setup, with the setting disabled. Teams can optionally have a logo image.<br/>
    Much like with event score, a team's score will increase when one of its members gains event currency (by the amount of currency gained). Additionally, once a team is created, its raw score can be adjusted. If weighting is enabled, the weighted score will also be displayed.
</p>

<p>
    Note that teams <strong>may not be removed</strong> while teams are enabled, as a safety precaution while an event is running.
</p>

{!! Form::open(['url' => 'admin/event-settings/teams', 'files' => true]) !!}
    <div class="text-right mb-3"><a href="#" class="btn btn-primary" id="add-team">Add Team</a></div>
    <div id="teamList">
        @foreach($teams as $team)
            <div class="input-group mb-2">
                @if($team->has_image)
                    <div class="input-group-prepend">
                        <span class="input-group-text"><a href="{{ $team->imageUrl }}" data-lightbox="entry" data-toggle="tooltip" title="Click to see full image"><img src="{{ $team->imageUrl }}" class="mw-100 mh-100" style="height:20px;"/></a></span>
                        <div class="input-group-text">
                            {!! Form::checkbox('remove_image['.$team->id.']', 1, 0, ['data-toggle' => 'tooltip', 'title' => 'Remove Image', 'aria-label' => 'Remove Image']) !!}
                        </div>
                    </div>
                @endif
                {!! Form::text('name['.$team->id.']', $team->name, ['class' => 'form-control', 'placeholder' => 'Team Name', 'aria-label' => 'Team Name']) !!}
                {!! Form::number('score['.$team->id.']', $team->score, ['class' => 'form-control', 'placeholder' => 'Team Score (Raw)', 'aria-label' => 'Team Score (Raw)']) !!}
                @if(Settings::get('event_weighting'))
                    {!! Form::number('weighted['.$team->id.']', $team->weightedScore, ['class' => 'form-control', 'placeholder' => 'Team Score (Weighted)', 'aria-label' => 'Team Score (Weighted)', 'disabled']) !!}
                @endif
                <div class="custom-file">
                    {!! Form::file('image['.$team->id.']', ['class' => 'custom-file-input', 'id' => 'team-'.$team->id]) !!}
                    {!! Form::label('image['.$team->id.']', 'Team Logo (Optional)', ['class' => 'custom-file-label']) !!}
                </div>
                <div class="input-group-append">
                    <a href="#" class="remove-team btn btn-danger" type="button" id="button-addon2" aria-label="Remove Team">×</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

<div class="team-row hide input-group mb-2">
    {!! Form::text('name[]', null, ['class' => 'form-control', 'placeholder' => 'Team Name', 'aria-label' => 'Team Name']) !!}
    <div class="custom-file">
        {!! Form::file('image[]', ['class' => 'custom-file-input']) !!}
        {!! Form::label('image[]', 'Team Logo (Optional)', ['class' => 'custom-file-label']) !!}
    </div>
    <div class="input-group-append">
        <a href="#" class="remove-team btn btn-danger" type="button" id="button-addon2" aria-label="Remove Team">×</a>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.clear-currency-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/event-settings/clear') }}", 'Clear Event Currency');
        });
    });

    $('#add-team').on('click', function(e) {
        e.preventDefault();
        addTeamRow();
    });
    $('.remove-team').on('click', function(e) {
        e.preventDefault();
        removeTeamRow($(this));
    })
    function addTeamRow() {
        var $clone = $('.team-row').clone();
        $('#teamList').append($clone);
        $clone.removeClass('hide team-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-team').on('click', function(e) {
            e.preventDefault();
            removeTeamRow($(this));
        })
        $clone.find('.team-select').selectize();
    }
    function removeTeamRow($trigger) {
        $trigger.parent().parent().remove();
    }

</script>

@endsection
