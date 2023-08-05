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

{!! Form::open(['url' => $theme->id ? 'admin/themes/edit/'.$theme->id : 'admin/themes/create', 'files' => true]) !!}

<h5>Basic Information</h5>

<div class="form-group row">
    <div class="col-md-auto my-auto">{!! Form::label('Name') !!}</div>
    <div class="col-md">{!! Form::text('name', $theme->name, ['class' => 'form-control']) !!}</div>
</div>

<p>If a theme isn't active it keeps it from being useable by any feature. <br/> Default may be overridden by conditional themes (like seasonal based if you add the weather extension), or user selected themes.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::checkbox('is_active', 1, $theme->id ? $theme->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_active', 'Is Selectable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view the theme.') !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::checkbox('is_default', 1, $theme->id ? $theme->is_default : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_default', 'Is Default', ['class' => 'form-check-label ml-3']) !!} {!! add_help('One at a time. Users with no theme selected default to this theme and logged out visitors default to this theme.') !!}
        </div>
    </div>
    <div class="col-md-5">
        <div class="is_user_selectable">
            {!! Form::checkbox('is_user_selectable', 1, $theme->id ? $theme->is_user_selectable : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_user_selectable', 'Is User Selectable by Default', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Is this a theme users can select freely? Themes granted by items should have this turned off.') !!}
        </div>
    </div>
</div>

{{-- TODO: --}}
@if(isset($conditions))
    <div class="row">
        <div class="col-md-6">
            TODO
        </div>
    </div>
@endif

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

<h5>CSS File</h5>
<p></p>
<div class="row">
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

<h5>Header Image</h5>
<p>The Header Image can be uploaded directly or specified by url. Finally you can turn the header off entirely and have just the top nav.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            @if($theme->has_header) <a href="{{ $theme->headerImageUrl }}"><i class="fas fa-link"></i></a> @endif
            {!! Form::label('Header Image') !!}
            <div>{!! Form::file('header') !!}</div>
            <div class="text-muted">Header image.</div>
            @if($theme->has_header)
                <div class="form-check">
                    {!! Form::checkbox('remove_header', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_header', 'Remove current header', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Header Image Url') !!}
            {!! Form::text('header_image_url', $theme->themeEditor->header_image_url ?? '', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('Show header image') !!}
        <div class="form-group">
            {!! Form::checkbox('header_image_display', 1,  $theme->themeEditor->header_image_display == 'inline' ?? 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
</div>

<h5>Background Image</h5>
<p>The Background Image can be uploaded directly or specified by url. If you only specify a color there will be no background image.</p>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            @if($theme->has_background) <a href="{{ $theme->backgroundImageUrl }}"><i class="fas fa-link"></i></a> @endif
            {!! Form::label('Background Image') !!}
            <div>{!! Form::file('background') !!}</div>
            <div class="text-muted">Background image.</div>
            @if($theme->has_background)
                <div class="form-check">
                    {!! Form::checkbox('remove_background', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_background', 'Remove current background', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Background Image Url') !!}
            {!! Form::text('background_image_url', $theme->themeEditor->background_image_url ?? '', ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select background color') !!}
            <div class="input-group cp">
                {!! Form::text('background_color', $theme->themeEditor->background_color ?? '#ddd', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        {!! Form::label('Background Repeat') !!}{!! add_help('If this is turned on, your background image will repeat to fill the page. If turned off, your background image will cover the width of the screen.') !!}
        <div class="form-group">
            {!! Form::checkbox('background_size', 1, $theme->themeEditor->background_size == 'cover' ?? 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
        </div>
    </div>
</div>
<hr>

<h5>Menu Bar</h5>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select title color') !!}
            <div class="input-group cp">
                {!! Form::text('title_color', $theme->themeEditor->title_color ?? '#ffffff', ['class' => 'form-control']) !!}
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
                {!! Form::text('nav_color', $theme->themeEditor->nav_color ?? '#343a40', ['class' => 'form-control']) !!}
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
                {!! Form::text('nav_text_color', $theme->themeEditor->nav_text_color ?? 'hsla(0,0%,100%,.5)', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
</div>

<hr/>

<h5>Main Content</h5>
<p>These colors also affect modal colors, the sidebar and input fields.</p>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('Select main content color') !!}
            <div class="input-group cp">
                {!! Form::text('main_color', $theme->themeEditor->main_color ?? '#ffffff', ['class' => 'form-control']) !!}
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
                {!! Form::text('main_text_color', $theme->themeEditor->main_text_color ?? '#000', ['class' => 'form-control']) !!}
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
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('Select card color') !!}
            <div class="input-group cp">
                {!! Form::text('card_color', $theme->themeEditor->card_color ?? '#ffffff', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('Select card text color') !!}
            <div class="input-group cp">
                {!! Form::text('card_text_color', $theme->themeEditor->card_text_color ?? '#000', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('Select card header color') !!}
            <div class="input-group cp">
                {!! Form::text('card_header_color', $theme->themeEditor->card_header_color ?? '#f1f1f1', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('Select card header text color') !!}
            <div class="input-group cp">
                {!! Form::text('card_header_text_color', $theme->themeEditor->card_header_text_color ?? '#fff', ['class' => 'form-control']) !!}
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
                {!! Form::text('link_color', $theme->themeEditor->link_color ?? '#000', ['class' => 'form-control']) !!}
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
                {!! Form::text('primary_button_color', $theme->themeEditor->primary_button_color ?? '#007bff', ['class' => 'form-control']) !!}
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
                {!! Form::text('secondary_button_color', $theme->themeEditor->secondary_button_color ?? '#6c757d', ['class' => 'form-control']) !!}
                <span class="input-group-append">
                    <span class="input-group-text colorpicker-input-addon"><i></i></span>
                </span>
            </div>
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
