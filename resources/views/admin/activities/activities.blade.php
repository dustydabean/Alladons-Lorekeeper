@extends('admin.layout')

@section('admin-title') Activities @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Activities' => 'admin/data/activities']) !!}

<h1>Activities</h1>

<p>This is a list of activities that users can interact with.</p> 
<p>The sorting order reflects the order in which the activities will be listed on the activity index.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/activities/create') }}"><i class="fas fa-plus"></i> Create New Activity</a></div>
@if(!count($activities))
    <p>No activies found.</p>
@else 
    <table class="table table-sm">
        <tbody id="sortable" class="sortable">
            @foreach($activities as $activity)
                <tr class="sort-item" data-id="{{ $activity->id }}">
                    <td>
                        <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                        {!! $activity->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/activities/edit/'.$activity->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    <div class="mb-4">
        {!! Form::open(['url' => 'admin/data/activities/sort']) !!}
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
        items: '.sort-item',
        handle: ".handle",
        placeholder: "sortable-placeholder",
        stop: function( event, ui ) {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        },
        create: function() {
            $('#sortableOrder').val($(this).sortable("toArray", {attribute:"data-id"}));
        }
    });
    $( "#sortable" ).disableSelection();
});
</script>
@endsection