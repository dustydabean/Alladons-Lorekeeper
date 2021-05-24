<h3 class="mb-3">Strength & Use Type</h3>

<div class="form-group">
    {!! Form::checkbox('reveal_strength', 1, $tag->getData()['reveal_strength'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => "Full Strength", 'data-off' => "Half Strength"]) !!}
    {!! Form::label('reveal_strength', 'Reveal Strength', ['class' => 'form-check-label ml-3']) !!}
    {!! add_help("Full Strength will fully reveal any genome. Half strength will half-reveal a hidden genome and fully reveal a half-hidden genome.") !!}
</div>

<div class="form-group">
    {!! Form::checkbox('fully_hidden_only', 1, $tag->getData()['fully_hidden_only'], ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => "Fully Hidden Only", 'data-off' => "Half & Fully Hidden"]) !!}
    {!! Form::label('fully_hidden_only', 'Usable On', ['class' => 'form-check-label ml-3']) !!}
    {!! add_help("Can this be used on only fully hidden genomes, or can it be used on half-hidden genomes as well?") !!}
</div>

<hr />
