<script>
    $(document).ready(function() {
        $('.edit-features').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/traits", 'Edit Traits');
        });
        $('.edit-notes').on('click', function(e) {
            e.preventDefault();
            $("div.imagenoteseditingparse").load("{{ url('admin/character/image') }}/" + $(this).data('id') + "/notes", function() {
                tinymce.init({
                    selector: '.imagenoteseditingparse .wysiwyg',
                    height: 500,
                    menubar: false,
                    convert_urls: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks fullscreen spoiler',
                        'insertdatetime media table paste codeeditor help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | codeeditor',
                    content_css: [
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
                    spoiler_caption: 'Toggle Spoiler',
                    target_list: false
                });
            });
            $(".edit-notes").remove();
        });
        $('.edit-credits').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/credits", 'Edit Image Credits');
        });
        $('.reupload-image').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/reupload", 'Reupload Image');
        });
        $('.active-image').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/active", 'Set Active');
        });
        $('.delete-image').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/delete", 'Delete Image');
        });
        $('.add-genome').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/'.($character->is_myo_slot ? 'myo' : 'character').'/') }}/{{ $character->is_myo_slot ? $character->id : $character->slug }}/genome/create", 'Add New Genome');
        });
        $('.edit-genome').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/'.($character->is_myo_slot ? 'myo' : 'character').'/') }}/{{ $character->is_myo_slot ? $character->id : $character->slug }}/genome/"+$(this).data('genome-id'), 'Edit Genome');
        });
        $('.delete-genome').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/'.($character->is_myo_slot ? 'myo' : 'character').'/') }}/{{ $character->is_myo_slot ? $character->id : $character->slug }}/genome/"+$(this).data('genome-id')+"/delete", 'Delete Genome');
        });
        $('.edit-stats').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/stats", 'Edit Character Stats');
        });
        $('.edit-lineage').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/lineage", 'Edit Character Lineage');
        });
        $('.edit-description').on('click', function(e) {
            e.preventDefault();
            $("div.descriptioneditingparse").load("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/description", function() {
                tinymce.init({
                    selector: '.descriptioneditingparse .wysiwyg',
                    height: 500,
                    menubar: false,
                    convert_urls: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks fullscreen spoiler',
                        'insertdatetime media table paste codeeditor help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | codeeditor',
                    content_css: [
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
                    spoiler_caption: 'Toggle Spoiler',
                    target_list: false
                });
            });
            $(".edit-description").remove();
        });
        $('.delete-character').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/delete", 'Delete Character');
        });
        $('.edit-image-colours').on('click', function(e) {
            e.preventDefault();
            $('#colour-collapse-' + $(this).data('id')).collapse('toggle');
        });
    });
</script>
