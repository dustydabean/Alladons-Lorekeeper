@extends('admin.layout')

@section('admin-title')
    Species
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Transformations' => 'admin/data/transformations']) !!}

    <h1>Transformations</h1>

    <p>Transformations are optional categories that can be added to species. Characters require a species, but do not require a transformation. Note that the sort order here reflects the sort order under the species name as well.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/transformations/create') }}"><i class="fas fa-plus"></i> Create New Transformation</a></div>
    @if (!count($transformations))
        <p>No transformations found.</p>
    @else
        <table class="table table-sm transformations-table">
            <tbody id="sortable" class="sortable">
                @foreach ($transformations as $transformation)
                    <tr class="sort-item" data-id="{{ $transformation->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            {!! $transformation->displayName !!}
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/transformations/edit/' . $transformation->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/data/transformations/sort']) !!}
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
                items: '.sort-item',
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
        });
    </script>
@endsection
