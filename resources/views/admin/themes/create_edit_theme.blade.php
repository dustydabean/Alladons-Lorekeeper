@extends('admin.layout')

@section('admin-title') {{ $theme->id ? 'Edit Theme: ' . $theme->name : 'Create Theme' }}  @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/themes', ($theme->id ? 'Edit' : 'Create').' Theme' => $theme->id ? 'admin/themes/edit/'.$theme->id : 'admin/themes/create']) !!}

<h1>{{ $theme->id ? 'Edit ' . $theme->name : 'Create Theme' }}
    @if($theme->id)
        <a href="#" class="btn btn-danger float-right delete-theme-button">Delete Theme</a>
    @endif
</h1>
@if($theme->creators) <h5>by {!! $theme->creatorDisplayName !!}</h5> @endif
<p class="text-danger">* All input fields are required.</p>

{!! Form::open(['url' => $theme->id ? 'admin/themes/edit/'.$theme->id : 'admin/themes/create', 'files' => true]) !!}

<h5>Basic Information</h5>

<div class="form-group row">
    <div class="col-md-auto my-auto">{!! Form::label('Name') !!}</div>
    <div class="col-md">{!! Form::text('name', $theme->name, ['class' => 'form-control']) !!}</div>
</div>

<h5>Creator(s)</h5>

<div class="row">
    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-md-auto my-auto">{!! Form::label('Creator(s) Name') !!} {!! add_help('Separate multiples via comma.') !!}</div>
            <div class="col-md">{!! Form::text('creator_name', $theme->creators ? $theme->creatorData['name'] : null, ['class' => 'form-control']) !!}</div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group row">
            <div class="col-md-auto my-auto">{!! Form::label('Creator(s) Url') !!} {!! add_help('Separate multiples via comma.') !!}</div>
            <div class="col-md">{!! Form::text('creator_url', $theme->creators ? $theme->creatorData['url'] : null, ['class' => 'form-control']) !!}</div>
        </div>
    </div>
</div>




<h5>Files</h5>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            @if($theme->has_header) <a href="{{ $theme->imageUrl }}"><i class="fas fa-link"></i></a> @endif
            {!! Form::label('Header Image') !!}
            <div>{!! Form::file('header') !!}</div>
            <div class="text-muted">Header image. Max file size: 1000 KB.</div>
            @if($theme->has_header)
                <div class="form-check">
                    {!! Form::checkbox('remove_header', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_header', 'Remove current header', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            @if($theme->has_css) <a href="{{ $theme->cssUrl }}"><i class="fas fa-link"></i></a> @endif
            {!! Form::label('CSS File') !!}
            <div>{!! Form::file('css') !!}</div>
            <div class="text-muted">Only CSS Files. Max file size: 1000 KB.</div>
            @if($theme->has_css)
                <div class="form-check">
                    {!! Form::checkbox('remove_css', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_css', 'Remove current css file', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::checkbox('is_active', 1, $theme->id ? $theme->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_active', 'Is Selectable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view the theme.') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::checkbox('is_default', 1, $theme->id ? $theme->is_default : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_default', 'Is Default', ['class' => 'form-check-label ml-3']) !!} {!! add_help('One at a time. Users with no theme selected default to this theme and logged out visitors default to this theme.') !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($theme->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary px-5']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {

    $('.delete-theme-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/themes/delete') }}/{{ $theme->id }}", 'Delete Theme');
    });

});
</script>
@endsection
