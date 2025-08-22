@if (!isset($tinymceScript) || $tinymceScript)
<script>
    $(document).ready(function() {
@endif
        tinymce.init({
            selector: '{{ $tinymceSelector ?? ".wysiwyg" }}',
            height: {{ $tinymceHeight ?? 500 }},
            menubar: false,
            convert_urls: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks fullscreen spoiler',
                'insertdatetime media table paste codeeditor help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | codeeditor',
            content_css: [
                '{{ asset('css/app.css?v=' . filemtime(public_path('css/app.css'))) }}',
                '{{ asset('css/lorekeeper.css?v=' . filemtime(public_path('css/lorekeeper.css'))) }}',
                '{{ asset('css/custom.css') }}',
                '{{ asset($theme?->cssUrl) }}',
                '{{ asset($conditionalTheme?->cssUrl) }}',
                '{{ asset($decoratorTheme?->cssUrl) }}',
                '{{ asset('css/all.min.css') }}' //fontawesome
            ],
            content_style: `
            {!! isset($theme) && $theme ? str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $theme])) : '' !!}
            {!! isset($conditionalTheme) && $conditionalTheme ? str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $conditionalTheme])) : '' !!}
            {!! isset($decoratorTheme) && $decoratorTheme ? str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $decoratorTheme])) : '' !!}
            `,
            spoiler_caption: 'Toggle Spoiler',
            target_list: false
        });
@if (!isset($tinymceScript) || $tinymceScript)
    });
</script>
@endif
