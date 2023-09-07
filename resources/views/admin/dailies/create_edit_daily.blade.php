@extends('admin.layout')

@section('admin-title') {{ucfirst(__('dailies.daily'))}} @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', ucfirst(__('dailies.daily')) => 'admin/data/dailies', ($daily->id ? 'Edit ' : 'Create ').ucfirst(__('dailies.daily'))
=> $daily->id ? 'admin/data/dailies/edit/'.$daily->id : 'admin/data/dailies/create']) !!}

<h1>{{ $daily->id ? 'Edit' : 'Create' }} {{ucfirst(__('dailies.daily'))}}
    @if($daily->id)
    ({!! $daily->displayName !!})
    <a href="#" class="btn btn-danger float-right delete-daily-button">Delete {{ucfirst(__('dailies.daily'))}}</a>
    @endif
</h1>

{!! Form::open(['url' => $daily->id ? 'admin/data/dailies/edit/'.$daily->id : 'admin/data/dailies/create', 'files' =>
true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $daily->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label(__('dailies.daily').' Image (Optional)') !!} {!! add_help('This image is used on the '.__('dailies.daily').' index and on the '.__('dailies.daily').'
     page as a header.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: None (Choose a standard size for all {{__('dailies.daily')}} images)</div>
    @if($daily->has_image)
    <div class="form-check">
        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
    </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Button Image (Optional)') !!} {!! add_help('This image is used for the button instead of the generic Collect Reward button.') !!}
    <div>{!! Form::file('button_image') !!}</div>
    <div class="text-muted">Recommended size: 200x200px or something small.</div>
    @if($daily->has_button_image)
    <div class="form-check">
        {!! Form::checkbox('remove_button_image', 1, false, ['class' => 'form-check-input']) !!}
        {!! Form::label('remove_button_image', 'Remove current button image', ['class' => 'form-check-label']) !!}
    </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $daily->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>Rewards</h3>
<p>Please add what reward the daily should award users each day. If you would like  an element of chance in it, linking a loot table here is recommended.</p>
<p>You can add loot tables containing any kind of currencies (both user- and character-attached), but be sure to keep track of which are being distributed! Character-only currencies cannot be given to users.</p>

@include('widgets._loot_select', ['loots' => $daily->rewards, 'showLootTables' => true, 'showRaffles' => true])


<div class="pl-4">
    <div class="form-group">
            {!! Form::checkbox('is_timed_daily', 1, $daily->is_timed_daily ?? 0, ['class' => 'form-check-input daily-timed daily-toggle daily-field', 'id' => 'is_timed_daily']) !!}
            {!! Form::label('is_timed_daily', 'Set Timed '.__('dailies.daily'), ['class' => 'form-check-label ml-3']) !!} {!! add_help('Sets the '.__('dailies.daily').' as timed between the chosen dates.') !!}
    </div>
    <div class="daily-timed-quantity {{ $daily->is_timed_daily ? '' : 'hide' }}">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('start_at', 'Start Time') !!} {!! add_help('The '.__('dailies.daily').' will cycle in at this date.') !!}
                    {!! Form::text('start_at', $daily->start_at, ['class' => 'form-control', 'id' => 'datepicker2']) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('end_at', 'End Time') !!} {!! add_help('The '.__('dailies.daily').' will cycle out at this date.') !!}
                    {!! Form::text('end_at', $daily->end_at, ['class' => 'form-control', 'id' => 'datepicker3']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row p-4">
    <div class="form-group col">
        {!! Form::checkbox('is_active', 1, $daily->id ? $daily->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_active', 'Set Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off,
        the '.__('dailies.daily').' will not be visible to regular users.') !!}
    </div>
    <div class="form-group col">
        {!! Form::checkbox('is_one_off', 1, $daily->id ? $daily->is_one_off : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_one_off', 'Is one-off', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Decides if the '.__('dailies.daily').' is claimable each day, or one time only.') !!}
    </div>
</div>

<br>

<div class="text-right">
    {!! Form::submit($daily->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>



{!! Form::close() !!}


@include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables, 'raffles' => $raffles, 'showLootTables' => true, 'showRaffles' => true])


@endsection

@section('scripts')
@parent
@include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])

<script>
$('#is_timed_daily').change(function() {
    if ($(this).is(':checked')) {
        $('.daily-timed-quantity').removeClass('hide');
    } else {
        $('.daily-timed-quantity').addClass('hide');
    }
});

$("#datepicker2").datetimepicker({
    dateFormat: "yy-mm-dd",
    timeFormat: 'HH:mm:ss',
});

$("#datepicker3").datetimepicker({
    dateFormat: "yy-mm-dd",
    timeFormat: 'HH:mm:ss',
});


$(document).ready(function() {


    $('.delete-daily-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/dailies/delete') }}/{{ $daily->id }}", 'Delete ' +"{{ucfirst(__('dailies.daily'))}}");
    });
    $('.add-daily-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/dailies/daily') }}/{{ $daily->id }}", 'Add Stock');
    });

    $('#add-feature').on('click', function(e) {
        e.preventDefault();
        addFeatureRow();
    });
    $('.remove-feature').on('click', function(e) {
        e.preventDefault();
        removeFeatureRow($(this));
    });

    function addFeatureRow() {
        var $clone = $('.feature-row').clone();
        $('#featureList').append($clone);
        $clone.removeClass('hide feature-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-feature').on('click', function(e) {
            e.preventDefault();
            removeFeatureRow($(this));
        })
        $clone.find('.feature-select').selectize();
    }

    function removeFeatureRow($trigger) {
        $trigger.parent().remove();
    }
});
</script>
@endsection