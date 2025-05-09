@extends('admin.layout')

@section('admin-title')
    {{ $feature->id ? 'Edit' : 'Create' }} Trait
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits', ($feature->id ? 'Edit' : 'Create') . ' Trait' => $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create']) !!}

    <h1>{{ $feature->id ? 'Edit' : 'Create' }} Trait
        @if ($feature->id)
        <a href="{{ url('admin/data/traits/examples/'.$feature->id) }}" class="btn btn-secondary float-right">Manage Examples</a>
            <a href="#" class="btn btn-danger float-right delete-feature-button">Delete Trait</a>
        @endif
    </h1>

    {!! Form::open(['url' => $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-4 form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $feature->name, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-4 form-group">
            {!! Form::label('Rarity') !!}
            {!! Form::select('rarity_id', $rarities, $feature->rarity_id, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-4 form-group">
            {!! Form::label('Code (Optional)') !!} {!! add_help('This displays before the name.') !!}
            {!! Form::text('code_id', $feature->code_id, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($feature->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-4 form-group">
            {!! Form::label('Trait Category (Optional)') !!}
            {!! Form::select('feature_category_id', $categories, $feature->feature_category_id, ['class' => 'form-control']) !!}
        </div>
        <div class="col-md-4 form-group">
            {!! Form::label('Species Restriction (Optional)') !!}
            {!! Form::select('species_id', $specieses, $feature->species_id, ['class' => 'form-control', 'id' => 'species']) !!}
        </div>
        <div class="col-md-4 form-group" id="subtypes">
            {!! Form::label('Species Content (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
            {!! Form::select('subtype_id', $subtypes, $feature->subtype_id, ['class' => 'form-control', 'id' => 'subtype']) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $feature->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Level (Optional)') !!}
                {!! Form::select('mut_level', ['1' => 'Minor', '2' => 'Major' ], $feature->mut_level, ['class' => 'form-control', 'placeholder' => 'Select a Level']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Type (Optional)') !!}
                {!! Form::select('mut_type', ['1' => 'Breed Only', '2' => 'Custom Requestable'], $feature->mut_type, ['class' => 'form-control', 'placeholder' => 'Select a Type']) !!}
            </div>
        </div>
        <div class="col-md-4 pt-md-4">
            <div class="form-group">
                {!! Form::checkbox('is_locked', 1, $feature->id ? $feature->is_locked : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_locked', 'Locked?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('When off, the mutation will be labelled as unlocked. Toggling this on will label the mutation as locked.') !!}
            </div>
        </div>
    </div>

    <div class="form-group pl-3 pl-md-0">
        {!! Form::checkbox('is_visible', 1, $feature->id ? $feature->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the trait will not be visible in the trait list or available for selection in search and design updates. Permissioned staff will still be able to add them to characters, however.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($feature->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($feature->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._feature_entry', ['feature' => $feature])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-feature-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/traits/delete') }}/{{ $feature->id }}", 'Delete Trait');
            });
            refreshSubtype();
        });

        $("#species").change(function() {
            refreshSubtype();
        });

        function refreshSubtype() {
            var species = $('#species').val();
            var subtype_id = {{ $feature->subtype_id ?: 'null' }};
            $.ajax({
                type: "GET",
                url: "{{ url('admin/data/traits/check-subtype') }}?species=" + species + "&subtype_id=" + subtype_id,
                dataType: "text"
            }).done(function(res) {
                $("#subtypes").html(res);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        };
    </script>
@endsection
