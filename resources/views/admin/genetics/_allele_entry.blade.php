<div class="input-group input-group-sm mb-1">
    <div class="input-group-prepend">
        @if($allele)
            <span class="input-group-text bg-info border-primary text-center">
                <a class="fas fa-arrows-alt-v handle" href="#"></a>
            </span>
        @endif
        <button class="btn btn-{{ $allele && $allele->is_dominant ? "dark" : "primary" }} hidden-check-toggle my-0 py-0" type="button" data-true-label="Dominant" data-false-label="Recessive">
            {{ $allele && $allele->is_dominant ? "Dominant" : "Recessive" }}
        </button>
        {!! Form::hidden(($allele ? 'edit_allele_dominance[]' : 'is_dominant[]'), $allele && $allele->is_dominant ? 1 : 0) !!}
    </div>
    {!! Form::text(($allele ? 'edit_' : '').'allele_name[]', $allele ? $allele->name : null, ['class' => 'form-control allele-letter-id', 'maxlength' => 5]) !!}
    {!! Form::text(($allele ? 'edit_allele_' : '').'modifier[]', $allele ? $allele->modifier : null, ['class' => 'form-control allele-modifier', 'maxlength' => 5]) !!}
    <div class="input-group-append">
        <span class="input-group-text preview text-monospace pb-0">{!! $allele ? $allele->displayName : '' !!}</span>
        @if($allele)
            <span class="input-group-text bg-light font-weight-bold text-monospace pb-0">{!! $allele ? $allele->displayName : '' !!}</span>
        @endif
    </div>
</div>

<div class="input-group input-group-sm">
    <div class="input-group-prepend">
        <button class="btn btn-{{ $allele && $allele->is_visible ? "dark" : "primary" }} hidden-check-toggle my-0 py-0" type="button" data-true-label="Visible" data-false-label="Hidden">
            {{ $allele && $allele->is_visible ? "Visible" : "Hidden" }}
        </button>
        {!! Form::hidden(($allele ? 'edit_' : '').'allele_visibility[]', $allele && $allele->is_visible ? 1 : 0) !!}
    </div>
    {!! Form::text(($allele ? 'edit_' : '').'allele_description[]', $allele ? $allele->summary : null, ['class' => 'form-control', 'placeholder' => "Short summary of allele.", 'maxlength' => 255]) !!}
    @if(!$allele)
        <div class="input-group-append">
            <button class="btn btn-danger delete-allele-row my-0 py-0" type="button"><i class="fas fa-times"></i></button>
        </div>
    @endif
</div>
