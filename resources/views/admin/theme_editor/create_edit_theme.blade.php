@extends('admin.layout')

@section('admin-name') Themes @endsection

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

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="favcolor">Select menu color.</label>
            <input class="w-100" type="color" name="nav_color" value="{{$theme->nav_color ? $theme->nav_color : '#ffffff'}}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="favcolor">Select menu text color.</label>
            <input class="w-100" type="color" name="nav_text_color" value="{{$theme->nav_text_color ? $theme->nav_text_color : '#ffffff'}}">
        </div>
    </div>
</div>

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


<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Background Image Url') !!}
            {!! Form::text('background_image_url', $theme->background_image_url, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('Background Repeat') !!}{!! add_help('If this is turned on, your background image will repeat to fill the page. If turned off, your background image will cover the width of the screen.') !!}
        <div class="form-group">
            {!! Form::checkbox('background_size', 1, $theme->background_size ? $theme->background_size == 'cover' : 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="favcolor">Select background color.</label>
            <input class="w-100" type="color" name="background_color" value="{{$theme->background_color ? $theme->background_color : '#ffffff'}}">
        </div>
    </div>
</div>





<div class="text-right">
    {!! Form::submit($theme->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

