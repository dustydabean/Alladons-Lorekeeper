@if (isset($noVariants) && $noVariants)
    {!! Form::select('variant_id', [], null, ['class' => 'form-control', 'placeholder' => 'No eligible variants for this pet and splice!', 'disabled']) !!}
@else
    {!! Form::select('variant_id', $variants, null, ['class' => 'form-control', 'placeholder' => 'Select a Variant']) !!}
@endif
