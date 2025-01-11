@extends('admin.layout')

@section('admin-title')
    Traits
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits', 'Manage Examples' => 'admin']) !!}

    <h1>{!! $feature->displayName !!}'s Example Images</h1>

    <p>Example images will be displayed either as a static image or carousel on the trait's entry page, below the main image for that trait. Click and drag + save to sort the order in which the images appear.</p>

    <div class="text-right mb-3">
        <a href="#" class="btn btn-primary create-ex-button"><i class="fas fa-plus"></i> Add New</a>
        <a class="btn btn-secondary" href="{{ url('admin/data/traits/create') }}"><i class="fas fa-folder"></i> Return to Traits</a>
    </div>

    @if (!count($examples))
        <p>No images found.</p>
    @else
        <table class="table table-sm category-table">
            <tbody id="sortable" class="sortable">
                @foreach ($examples as $example)
                    <tr class="sort-border" data-id="{{ $example->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            <img src="{{ $example->imageUrl }}" class="world-entry-image" alt="{{ $feature->name . ' example' }}" style="max-height:10em;" /></a>
                        </td>
                        <td>
                            <a href="#" class="btn btn-info text-white mr-2 edit-ex-button-{{ $example->id }}"><i class="fas fa-pencil-alt"></i></a>
                            <button class="btn btn-danger delete-ex-button-{{ $example->id }}" data-id="{{ $example->id }}"><i class="fas fa-trash"></i></button>
                            <script>
                                $(document).ready(function() {
                                    $('.edit-ex-button-{{ $example->id }}').on('click', function(e) {
                                        e.preventDefault();
                                        loadModal("{{ url('admin/data/traits/examples/' . $feature->id . '/edit') }}/{{ $example->id }}", 'Edit Example');
                                    });
                                    $('.delete-ex-button-{{ $example->id }}').on('click', function(e) {
                                        e.preventDefault();
                                        loadModal("{{ url('admin/data/traits/examples/delete') }}/{{ $example->id }}", 'Delete Example');
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/data/traits/examples/' . $feature->id . '/sort']) !!}
            {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
            {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.handle').on('click', function(e) {
                e.preventDefault();
            });
            $("#sortable").sortable({
                borders: '.sort-border',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
            });
            $("#sortable").disableSelection();
            $('.create-ex-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/traits/examples/' . $feature->id . '/create') }}", 'Create Example');
            });
        });
    </script>
@endsection
