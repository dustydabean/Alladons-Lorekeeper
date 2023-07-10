<h4>Collection to be used for this Activity</h4>
<p>The collection will be heavily featured on the page as it will be just a single collection that the user can redeem for the activity.</p>
<p>We heavily suggest using a <i>hidden</i> collection if you make use of the collection extension in the rest of the site, so that it does not confuse users to see it in both places</p>

<div class="form-group w-50">
    {!! Form::label('Collection To Display') !!}
    {!! Form::select('collection_id', $collections, $activity->data, ['class' => 'form-control', 'placeholder' => 'Select a Collection']) !!}
</div>