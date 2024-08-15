tinymce.init({
selector: '#modal .wysiwyg',
height: 500,
menubar: false,
plugins: [
'advlist autolink lists link image charmap print preview anchor',
'searchreplace visualblocks code fullscreen',
'insertdatetime media table paste code help wordcount'
],
toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
content_css: [
'//www.tiny.cloud/css/codepen.min.css',
'{{ asset('css/app.css') }}',
'{{ asset('css/lorekeeper.css') }}',
'{{ asset('css/custom.css') }}',
'{{ asset($theme?->cssUrl) }}',
'{{ asset($conditionalTheme?->cssUrl) }}',
'{{ asset($decoratorTheme?->cssUrl) }}',
'{{ asset('css/all.min.css') }}' //fontawesome
],
content_style: `
{{ str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $theme])) }}
{{ str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $conditionalTheme])) }}
{{ str_replace(['<style>', '</style>'], '', view('layouts.editable_theme', ['theme' => $decoratorTheme])) }}
`,
});
