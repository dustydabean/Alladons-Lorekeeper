<script>
    $(document).ready(function() {
        $('.edit-features').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/image') }}/" + $(this).data('id') + "/traits", 'Edit Traits');
        });
        $('.edit-notes').on('click', function(e) {
            e.preventDefault();
            $("div.imagenoteseditingparse").load("{{ url('admin/character/image') }}/" + $(this).data('id') + "/notes");
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
        $('.edit-breeding-slot').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/character/') }}/breeding-slot/"+$(this).data('id'), 'Edit Breeding Slot');
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
            $("div.descriptioneditingparse").load("{{ url($character->is_myo_slot ? 'admin/myo/' : 'admin/character/') }}/" + $(this).data('{{ $character->is_myo_slot ? 'id' : 'slug' }}') + "/description");
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
