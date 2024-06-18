@extends('admin.layout')

@section('admin-title')
    Traits
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits', ($feature->id ? 'Edit' : 'Create') . ' Trait' => $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create']) !!}

    <h1>{{ $feature->id ? 'Edit' : 'Create' }} Trait
        @if ($feature->id)
            <a href="#" class="btn btn-danger float-right delete-feature-button">Delete Trait</a>
        @endif
    </h1>

    {!! Form::open(['url' => $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $feature->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Rarity') !!}
                {!! Form::select('rarity_id', $rarities, $feature->rarity_id, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
        <div>{!! Form::file('image') !!}</div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($feature->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 form-group">
            {!! Form::label('Example Image (Optional)') !!} {!! add_help('This image is used only on the world information pages. This image should be supplied as an additional example image for the trait, and isn\'t the main image.') !!}
            <div>{!! Form::file('example_image') !!}</div>
            <div class="text-muted">Recommended size: 200px x 200px</div>
            @if ($feature->has_example_image)
                <div class="form-check">
                    {!! Form::checkbox('remove_example_image', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_example_image', 'Remove current example image', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
        <div class="col-md-6 form-group">
            {!! Form::label('Example Summary (Optional)') !!} {!! add_help('This is a short blurb that shows up under the example image. HTML cannot be used here.') !!}
            {!! Form::text('example_summary', $feature->example_summary, ['class' => 'form-control', 'maxLength' => 250]) !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Trait Category (Optional)') !!}
                {!! Form::select('feature_category_id', $categories, $feature->feature_category_id, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Species Restriction (Optional)') !!}
                {!! Form::select('species_id', $specieses, $feature->species_id, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Subtype (Optional)') !!} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
                {!! Form::select('subtype_id', $subtypes, $feature->subtype_id, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $feature->description, ['class' => 'form-control wysiwyg']) !!}
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
    <script>
        $(document).ready(function() {
            $('.delete-feature-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/traits/delete') }}/{{ $feature->id }}", 'Delete Trait');
            });
        });
    </script>
@endsection
