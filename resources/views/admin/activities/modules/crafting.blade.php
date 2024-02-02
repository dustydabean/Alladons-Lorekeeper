<h4>Recipes to be used for this Activity</h4>
<p>The recipes will be heavily featured on the page as it will be only a few recipes that a user can complete from here.</p>
<p>We heavily suggest using a <i>hidden / requires unlocking</i> recipe if you make use of the crafting extension in the rest of the site, so that it does not confuse users to see it in both places</p>
<p>You only need to fill in the inputs for the number of recipes you wish to display.

<h5>Recipes</h5>
<div class="recipes">
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[0] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[1] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[2] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[3] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[4] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
    <div class="form-group w-50">
        {!! Form::select('recipe_id[]', $recipes, $activity->data[5] ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Recipe']) !!}
    </div>
</div>