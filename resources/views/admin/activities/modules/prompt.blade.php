<h4>Prompt to be used for this Activity</h4>
<p>The prompt list here is auto-filtered for hidden prompts under the assumption that you wouldn't want it also listed under the prompt page</p>
<p>The selected prompt will be what submissions show up in under the admin queue, and is the source of default rewards.</p>

<div class="form-group w-50">
    {!! Form::label('Associated Prompt') !!}
    {!! Form::select('prompt_id', $prompts, $activity->data->prompt_id ?? null, ['class' => 'form-control', 'placeholder' => 'Select a Prompt']) !!}
</div>

<div class="form-group">
    {!! Form::label('Template for Prompt (optional)') !!}{!! add_help('Will auto-fill the prompt textarea with a template for the user to fill out') !!}
    {!! Form::textarea('template', $activity->data->template ?? null, ['class' => 'form-control wysiwyg']) !!}
</div>
<div class="row">
    <div class="col-md-4">
        {!! Form::checkbox('show_rewards', 1, $activity->data->show_rewards ?? 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('show_rewards', 'Show Rewards', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Show a fillable rewards section on the activity, similar to standard prompt rewards.') !!}
    </div>

    <div class="col-md-4">
        {!! Form::checkbox('choose_reward', 1, $activity->data->choose_reward ?? 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('choose_reward', 'User Chooses Reward', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Makes it so the user has to choose between any individual reward specified on the prompt. Want to let them choose between multiple sets? Use a box item!') !!}
    </div>
</div>
