@extends('admin.layout')

@section('admin-title') Dev Logs @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Dev Logs' => 'admin/logs', ($devLogs->id ? 'Edit' : 'Create').' Post' => $devLogs->id ? 'admin/logs/edit/'.$devLogs->id : 'admin/logs/create']) !!}

<h1>{{ $devLogs->id ? 'Edit' : 'Create' }} Log
    @if($devLogs->id)
        <a href="#" class="btn btn-danger float-right delete-logs-button">Delete Post</a>
    @endif
</h1>

{!! Form::open(['url' => $devLogs->id ? 'admin/logs/edit/'.$devLogs->id : 'admin/logs/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Title') !!}
            {!! Form::text('title', $devLogs->title, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Post Time (Optional)') !!} {!! add_help('This is the time that the dev log should be posted. Make sure the Is Viewable switch is off.') !!}
            {!! Form::text('post_at', $devLogs->post_at, ['class' => 'form-control', 'id' => 'datepicker']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Post Content') !!}
    {!! Form::textarea('text', $devLogs->text, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $devLogs->id ? $devLogs->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Is Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the post will not be visible. If the post time is set, it will automatically become visible at/after the given post time, so make sure the post time is empty if you want it to be completely hidden.') !!}
        </div>
    </div>
    @if($devLogs->id && $devLogs->is_visible)
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('bump', 1, null, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('bump', 'Bump Logs', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If toggled on, this will alert users that there is new dev logs. Best in conjunction with a clear notification of changes!') !!}
            </div>
        </div>
    @endif
</div>

<div class="text-right">
    {!! Form::submit($devLogs->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-logs-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/logs/delete') }}/{{ $devLogs->id }}", 'Delete Post');
    });
    $( "#datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'HH:mm:ss',
    });
});
    
</script>
@endsection