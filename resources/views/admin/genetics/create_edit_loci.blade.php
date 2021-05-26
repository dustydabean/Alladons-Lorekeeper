@extends('admin.layout')

@section('admin-title') Trait Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/genetics/genes', ($category->id ? 'Edit' : 'Create').' Gene Group' => $category->id ? 'admin/data/genetics/edit/'.$category->id : 'admin/data/genetics/create']) !!}

<h1>{{ $category->id ? 'Edit' : 'Create' }} Gene Group
    @if($category->id)
        <a href="#" class="btn btn-danger float-right delete-category-button">Delete Gene Group</a>
    @endif
</h1>
@if(!$category->id)
    <p class="alert alert-info">Alleles can only be created after the gene is made.</p>
@endif

{!! Form::open(['url' => $category->id ? 'admin/genetics/edit/'.$category->id : 'admin/genetics/create']) !!}

<h3>Basic Information</h3>

<div class="row mb-3">
    <div class="col-6">
        <div class="form-group">
            {!! Form::label('name', "Name") !!}
            {!! Form::text('name', $category->name, ['class' => 'form-control', 'maxlength' => 20]) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {!! Form::label('type', 'Type') . add_help("Standard are normal genes (eg. Aa). Gradient is for genes like rufus factors, with a large array of  binary on/off genes (eg. +-+--+++-+), and numbers are for straight up numbers (eg. 50, 12, 251). Type cannot be changed after creation.") !!}
            {!! Form::select('type', ["gene" => "Standard", "gradient" => "Gradient", "numeric" => "Numeric"], $category->type, ['class' => 'form-control', 'disabled' => $category->id != null]) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {!! Form::label('length', 'Length') . add_help("For standard genes, this should be set to 2 (one for each allele in the pair). For gradients, it should be a multiple of 2 (one for the maternal set and one for the paternal set) and cannot be larger than 64. For numbers, it can be anything between 0 and 255.") !!}
            {!! Form::number('length', $category->id ? $category->length : 2, ['class' => 'form-control', 'min' => 0, 'max' => 255]) !!}
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            {!! Form::label('default', 'Default Inheritance') . add_help("Used to determine what happens when one parent has this gene and the other doesn't. Can only be set after creation.") !!}
            {!! Form::select('default', $defaultOptions, $category->id ? $category->default : 0, ['class' => 'form-control', 'disabled' => $category->id == null]) !!}
        </div>
    </div>
</div>

@if($category->id && $category->type == 'gene')
    <h3>Create Alleles</h3>
    <p>Add new alleles to this gene group here. The checkbox determines dominance. The first text area determines the identifier and the second is an optional modifier. Use the textbox next to the add allele button to set all the identifiers at once.</p>
    <div id="allele-creation"></div>
    <div class="form-inline mb-3">
        <div class="form-group mr-3 mb-3">
            <a href="#" class="add-allele-button btn btn-primary"><i class="fas fa-plus"></i> New Allele</a>
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::text('default-allele', null, ['class' => 'allele-letter-override form-control', 'maxlength' => 5]) !!}
        </div>
    </div>

    <h3>Edit Alleles</h3>
    @if(count($category->alleles) == 0)
        <p>This gene group doesn't have any alleles!</p>
    @else
        <p>
            Edit existing alleles and sort them by dominance. No matter what sort order you save these as, genes marked as dominant will always appear before ones unmarked as dominant (recessive) ones.
            {!! Form::hidden('allele_sort', '', ['id' => 'sortableOrder']) !!}
        </p>
        <div class="card mb-3">
            <ul id="sortable" class="sortable list-group list-group-flush">
                @foreach($category->alleles as $alleles)
                    <li class="sort-item list-group-item" data-id="{{ $alleles->id }}">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-info border-primary text-center">
                                    <a class="fas fa-arrows-alt-v handle" href="#"></a>
                                </span>
                                <span class="input-group-text py-0 " id="basic-addon3">
                                    {!! Form::checkbox('edit_allele_dominance[]', true, $alleles->is_dominant, ['class' => "form-check-input ml-0 mt-0 mb-n1"]) !!}
                                    {!! Form::label('edit_allele_dominance[]', "Dominant", ['class' => "ml-3 pl-1 form-check-label"]) !!}
                                </span>
                            </div>
                            {!! Form::text('edit_allele_name[]', $alleles->name, ['class' => 'form-control allele-letter-id', 'maxlength' => 5]) !!}
                            {!! Form::text('edit_allele_modifier[]', $alleles->modifier, ['class' => 'form-control allele-modifier', 'maxlength' => 5]) !!}
                            <div class="input-group-append">
                                <span class="input-group-text preview text-monospace pb-0">{!! $alleles->displayName !!}</span>
                                <span class="input-group-text bg-light font-weight-bold text-monospace pb-0">{!! $alleles->displayName !!}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="form-inline float-left">
            <div class="form-group mr-3 mb-3">
                <a href="#" class="delete-allele-button btn btn-danger"><i class="fas fa-minus mr-2"></i> Delete Allele</a>
            </div>
            <div class="form-group mr-3 mb-3">
                {!! Form::text('override-allele', null, ['class' => 'allele-letter-override form-control', 'maxlength' => 5]) !!}
            </div>
        </div>
    @endif
@elseif($category->id)
    <h3>Gene Details</h3>
    <div class="card mb-4">
        @if ($category->type == "gradient")
            <div class="card-body">
                <p>Gradient of length {{ $category->length }}, meaning it can be any whole number between 0 and {{ $category->length }}.</p>
                @php
                    $val = 0; $eg = "";
                    for ($i = 0; $i < $category->length; $i++)
                    {
                        $rand = mt_rand(0, 1);
                        $val += $rand;
                        $eg .= $rand == 1 ? "+" : "-";
                    }
                @endphp
                <strong>Example</strong>: <code>{{ $eg }}</code> ({{ $val }})
            </div>
        @elseif ($category->type == "numeric")
            <div class="card-body">
                <p>Number of length {{ $category->length }}, meaning it can be any whole number between 0 and {{ $category->length }}.</p>
                <strong>Example</strong>: {{ mt_rand(0, $category->length) }}
            </div>
        @endif
    </div>
@endif

<div class="text-right">
    {!! Form::submit($category->id ? 'Save Changes' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

{{-- Form Template for Allele creation. --}}
@if ($category->id && $category->type == 'gene')
    <div class="allele-creation-row hide mb-2">
        <div class="input-group input-group-sm mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text py-0">
                    {!! Form::checkbox('is_dominant[]', true, false, ['class' => "form-check-input ml-0 mt-0 mb-n1"]) !!}
                    {!! Form::label('is_dominant[]', "Dominant", ['class' => "ml-3 pl-1 form-check-label"]) !!}
                </span>
            </div>
            {!! Form::text('allele_name[]', null, ['class' => 'form-control allele-letter-id', 'maxlength' => 5]) !!}
            {!! Form::text('modifier[]', null, ['class' => 'form-control allele-modifier', 'maxlength' => 5]) !!}
            <div class="input-group-append">
                <span class="input-group-text preview text-monospace pb-0"></span>
                <button class="btn btn-danger delete-allele-row my-0 py-0" type="button"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-category-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/genetics/delete') }}/{{ $category->id }}", 'Delete Category');
    });

    @if($category->id && $category->type == 'gene')
        $alleleCloneTarget = $('.allele-creation-row');
        addAlleleListeners($alleleCloneTarget);
        addAlleleListeners($('#sortable'));
        $('.add-allele-button').on('click', function(e) {
            e.preventDefault();
            $clone = $alleleCloneTarget.clone();
            $clone.removeClass('.allele-creation-row hide');
            addAlleleListeners($clone);
            $clone.find('.form-check-input').on('click', function(e) {
                updateAllelePreview($(this).parent().parent().parent());
            });
            $clone.find('.form-control').on('change', function(e) {
                updateAllelePreview($(this).parent().parent());
            });
            $('#allele-creation').append($clone);
        });
        $('.delete-allele-button').on('click', function(e) {
            e.preventDefault();
            loadModal("{{ url('admin/genetics/delete-allele/'.$category->id) }}", 'Delete Allele');
        });
        function addAlleleListeners($clone)
        {
            $button = $clone.find('.delete-allele-row');
            $button.on('click', function(e) {
                $(this).parent().parent().remove();
            });

            $inputs = $clone.find('input');
            $inputs.on('change', function(e) {
                updateAllelePreview($(this).parent());
            });
        }
        function updateAllelePreview($clone)
        {
            $preview = $clone.find('.preview');
            $isDominant = $clone.find('input[type=checkbox]').is(":checked");

            $gene = $clone.find('.allele-letter-id').val();
            $mod = $clone.find('.allele-modifier').val();

            $preview.children().remove();
            $preview.text($isDominant ? $gene : $gene.toLowerCase());
            $preview.append($("<sup></sup>").text($isDominant ? $mod : $mod.toLowerCase()));
        }
        $(".allele-letter-override").change(function(e) {
            $alleles = $(document).find(".allele-letter-id");
            $alleles.val($(this).val());
            $alleles.trigger("change");
        });

        // Sortable
        $('.handle').on('click', function(e) {
            e.preventDefault();
        });
        $('#sortable').sortable({
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
        $('#sortable').disableSelection();
    @endif
});
</script>
@endsection
