@if(!$genome)
    Unknown
@else
    @php
        $visible = 0;
        if(Auth::user()->hasPower('view-genomes')) $visible = 2;
        else if ($genome->visibility_level) $visible = $genome->visibility_level;
        else $visible = Settings::get('genome_default_visibility');
    @endphp
    @if($visible < 1)
        Hidden <a href="" class="btn btn-sm btn-link"><i class="fas fa-search"></i></a>
    @else
        @php
            $bool = $visible == 1;
            foreach ($genome->getLoci() as $loci) {
                echo('<div class="d-inline text-nowrap text-monospace mr-2" data-toggle="tooltip" title="'. $loci->name .'"">');
                if ($loci->type == "gene") {
                    if($bool)
                        foreach ($loci->alleles as $allele)
                            foreach ($genome->genes->where('loci_allele_id', $allele->id) as $item) {
                                echo($item->allele->displayName . "-");
                                break(2);
                            }
                    else
                        foreach ($genome->genes->where('loci_id', $loci->id) as $item)
                            echo($item->allele->displayName);
                }
                elseif ($loci->type == "gradient")
                    foreach ($genome->gradients->where('loci_id', $loci->id) as $item)
                        echo($bool ? $item->displayValue : $item->displayGenome);
                elseif ($loci->type == "numeric")
                    foreach ($genome->numerics->where('loci_id', $loci->id) as $item)
                        echo($bool ? $item->estValue : $item->value);
                echo('</div>');
            }
            if($genome->visibility_level != 2 && Auth::user()->hasPower('view-genomes'))
                echo add_help("This character's genome is either fully or partially hidden. You can only view its details because of your rank.");
        @endphp
    @endif
@endif
