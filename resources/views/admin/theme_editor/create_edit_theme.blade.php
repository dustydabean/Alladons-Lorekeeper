@extends('admin.layout')

@section('admin-title') Themes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Theme Editor' => 'admin/theme-editor', ($theme->id ? 'Edit' : 'Create').' Theme' => $theme->id ? 'admin/themes/edit/'.$theme->id : 'admin/themes/create']) !!}



<h1>{{ $theme->id ? 'Edit' : 'Create' }} Theme
    @if($theme->id)
    {!! Form::open(['url' => '/admin/theme-editor/delete/'.$theme->id]) !!}
    {!! Form::submit('Delete Theme', ['class' => 'btn btn-danger float-right']) !!}
    {!! Form::close() !!}
    @endif
</h1>

<p>
You can create or edit a theme here! Beware that if the theme you are editing is the currently active one as per the site settings, it will update as you edit it for everyone.
</p>

{!! Form::open(['url' => $theme->id ? 'admin/theme-editor/edit/'.$theme->id : 'admin/theme-editor/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $theme->name, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<h5>Menu Bar</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select title color') !!}
            <div class="input-group cp">
                {!! Form::text('title_color', $theme->title_color ? $theme->title_color : '#ffffff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select menu color') !!}
            <div class="input-group cp">
                {!! Form::text('nav_color', $theme->nav_color ? $theme->nav_color : '#343a40', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select menu text color') !!}
            <div class="input-group cp">
                {!! Form::text('nav_text_color', $theme->nav_text_color ? $theme->nav_text_color : '#ffffff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>
<hr>
<h5>Header Image</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Header Image Url') !!}
            {!! Form::text('header_image_url', $theme->header_image_url, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('Show header image') !!}
        <div class="form-group">
            {!! Form::checkbox('header_image_display', 1, $theme->header_image_display ? $theme->header_image_display == 'inline' : 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
</div>

<hr>
<h5>Background Image</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Background Image Url') !!}
            {!! Form::text('background_image_url', $theme->background_image_url, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select background color') !!}
            <div class="input-group cp">
                {!! Form::text('background_color', $theme->background_color ? $theme->background_color : '#ddd', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('Background Repeat') !!}{!! add_help('If this is turned on, your background image will repeat to fill the page. If turned off, your background image will cover the width of the screen.') !!}
        <div class="form-group">
            {!! Form::checkbox('background_size', 1, $theme->background_size ? $theme->background_size == 'cover' : 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
</div>
<hr>
<h5>Main Content</h5>
<p>These colors also affect modal colors, the sidebar and input fields.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select main content color') !!}
            <div class="input-group cp">
                {!! Form::text('main_color', $theme->main_color ? $theme->main_color : '#ffffff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select main text color') !!}
            <div class="input-group cp">
                {!! Form::text('main_text_color', $theme->main_text_color ? $theme->main_text_color : '#000', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>
<hr>
<h5>Card Content</h5>
<p>These colors also affect list groups and the nav tabs.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select card color') !!}
            <div class="input-group cp">
                {!! Form::text('card_color', $theme->card_color ? $theme->card_color : '#ffffff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select card header color') !!}
            <div class="input-group cp">
                {!! Form::text('card_header_color', $theme->card_header_color ? $theme->card_header_color : '#f1f1f1', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select card text color') !!}
            <div class="input-group cp">
                {!! Form::text('card_text_color', $theme->card_text_color ? $theme->card_text_color : '#000', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>
<hr>
<h5>Links & Buttons</h5>
<p>Primary and secondary buttons will use the same text color as links.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select link color') !!}
            <div class="input-group cp">
                {!! Form::text('link_color', $theme->link_color ? $theme->link_color : '#000', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select primary button color') !!}
            <div class="input-group cp">
                {!! Form::text('primary_button_color', $theme->primary_button_color ? $theme->primary_button_color : '#007bff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select secondary button color') !!}
            <div class="input-group cp">
                {!! Form::text('secondary_button_color', $theme->secondary_button_color ? $theme->secondary_button_color : '#6c757d', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>
<hr>
<h5>Release</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Is released') !!}{!! add_help('If this is turned on, users can select the theme in their theme settings without needing to unlock it.') !!}
            {!! Form::checkbox('is_released', 1, $theme->is_released ? $theme->is_released : 0, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($theme->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

