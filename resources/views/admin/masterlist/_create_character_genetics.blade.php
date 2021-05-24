@if($loci->type == 'gene')
    @for ($i = 0; $i < $loci->length; $i++)
        {!! Form::select('gene_allele_id[]', $alleles, null, ['class' => 'form-control allele-select']) !!}
    @endfor
@elseif($loci->type == 'numeric')
    {!! Form::number('gene_numeric_data[]', 0, ['class' => 'form-control', 'min' => 0, 'max' => $loci->length]) !!}
@else
    @php $bin = ""; for ($i=0; $i < $loci->length; $i++) $bin .= $i % 2 == 1 ? '+' : '-'; @endphp
    {!! Form::text('gene_gradient_data[]', $bin, ['class' => 'form-control gradient-gene-input', 'maxlength' => $loci->length]) !!}
@endif
