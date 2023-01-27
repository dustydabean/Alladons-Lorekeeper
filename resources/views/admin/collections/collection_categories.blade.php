@extends('admin.layout')

@section('admin-title') Collection Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Collection Categories' => 'admin/data/collections/collection-categories']) !!}


<div class="text-right mb-3">
<a class="btn btn-primary" href="{{ url('admin/data/collections') }}"> Collection Home</a>
</div>
<h1>Collection Categories</h1>

<p>This is a list of collection categories that will be used to sort collections in the inventory. Creating collection categories is entirely optional, but recommended if you have a lot of collections in the game.</p> 
<p>The sorting order reflects the order in which the collection categories will be displayed in the inventory, as well as on the world pages.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/collections/collection-categories/create') }}"><i class="fas fa-plus"></i> Create New Collection Category</a></div>
@if(!count($categories))
    <p>No collection categories found.</p>
@else 
    <table class="table table-sm category-table">
        <tbody id="sortable" class="sortable">
            @foreach($categories as $category)
                <tr class="sort-collection" collection-id="{{ $category->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $category->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/collections/collection-categories/edit/'.$category->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/data/collections/collection-categories/sort']) !!}
        {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>

$( document ).ready(function() {
    $('.handle').on('click', function(e) {
        e.preventDefault();
    });
    $( "#sortable" ).sortable({
        collections: '.sort-collection',
        handle: ".handle",
        placeholder: "sortable-placeholder",
        stop: function( event, ui ) {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"collection-id"}));
        },
        create: function() {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"collection-id"}));
        }
    });
    $( "#sortable" ).disableSelection();
});
</script>
@endsection