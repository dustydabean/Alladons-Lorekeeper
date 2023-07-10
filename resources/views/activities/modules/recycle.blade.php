{!! Form::open(['url' => 'activities/' . $activity->id . '/act']) !!}
<h3>Select Items to Turn In</h3>
@if($activity->data->quantity)
<p>You are required to select {{ $activity->data->quantity }} item(s) to turn in.</p>
@endif
@include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => [], 'hideCollapse' => true])
<div class="text-right">
    {!! Form::submit('Turn in Items', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._inventory_select_js')