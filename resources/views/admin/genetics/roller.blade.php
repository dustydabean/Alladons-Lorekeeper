@extends('admin.layout')

@section('admin-title') Trait Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Genetics' => 'admin/data/genetics', 'Roller' => 'admin/data/genetics']) !!}

<h1>Breeding Roller</h1>
<p><strong>Traits are not heritable or set automatically.</strong> This is just for genome creation via inheritance.</p>

<hr>
<div class="row align-items-end">
    @for ($i = 0; $i < 2; $i++)
        <div class="col-md-6">
            <div class="card mb-3 mb-md-0">
                <div class="card-header">
                    <h5 class="mb-0 text-center">{{ $i == 0 ? "Matrilineage" : "Patrilineage"}}</h5>
                </div>
                <div class="card-body">
                    {!! Form::select('parent[]', [0 => "Select Character"] + $characters, 0, ['id' => $i==0 ? "mother" : "father", 'class' => 'form-control parent-select']) !!}
                </div>
                <ul class="list-group list-group-flush genomes"></ul>
            </div>
        </div>
    @endfor
</div>

<hr>
<div class="row justify-content-center breeding-result">
    <div class="col-12 col-md-6">
        <h3>Roller Settings</h3>
        <div class="row no-gutters">
            @php
                $col = "col-12 col-sm-6 col-md-12 col-xl-6 ";
                $left = "pr-sm-2 pr-md-0 pr-xl-2";
                $right = "pl-sm-2 pl-md-0 pl-xl-2";
            @endphp
            <div class="{{ $col.$left }}">
                {!! Form::label('min_offspring', "Litter Min & Max Size") !!}
                <div class="input-group mb-3">
                    {!! Form::number('min_offspring', 0, ['class' => 'form-control', 'id' => "size_min", 'min' => 0]) !!}
                    {!! Form::number('max_offspring', 1, ['class' => 'form-control', 'id' => "size_max", 'min' => 1]) !!}
                </div>
            </div>
            <div class="{{ $col.$right }}">
                {!! Form::label('twin_chance', "Twin % Chance") !!} & {!! Form::label('twin_depth', "Max Depth") !!}
                <div class="input-group mb-3">
                    {!! Form::number('twin_chance', 0, ['class' => 'form-control', 'id' => "twin_chance", 'min' => 0, 'max' => 100]) !!}
                    {!! Form::number('twin_depth', 1, ['class' => 'form-control', 'id' => "twin_depth", 'min' => 1]) !!}
                </div>
            </div>
            <div class="{{ $col.$left }}">
                {!! Form::label('chimera_chance', "Chimerism % Chance") !!} & {!! Form::label('chimera_depth', "Max Genomes") !!}
                <div class="input-group mb-md-3 mb-xl-0">
                    {!! Form::number('chimera_chance', 0, ['class' => 'form-control', 'id' => "chimera_chance", 'min' => 0, 'max' => 100]) !!}
                    {!! Form::number('chimera_depth', 1, ['class' => 'form-control', 'id' => "chimera_depth", 'min' => 1]) !!}
                </div>
            </div>
            <div class="{{ $col.$right }} d-flex">
                <a href="#" class="btn btn-success preview-breeding mt-auto w-100"><i class="fas fa-dna mr-1"></i>Preview<i class="fas fa-dna ml-1"></i></a>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card mb-md-0">
            <div class="card-header">
                <h5 class="mb-0 text-center">Roller Preview</h5>
            </div>
            <ul class="list-group list-group-flush child-genomes"></ul>
        </div>
    </div>
</div>
<hr>

@endsection

@section('scripts')
@parent
<script>
    $(document).ready(function() {
        $('.parent-select').selectize();
        $('.parent-select').change(function(e) {
            var id = $(this).val();
            var genomes = $(this).parent().parent().find('.genomes');
            if (id < 1) {
                genomes.html("");
                return;
            }
            makeRequest("{{ url('admin/genetics/fetch-genomes') }}?id="+ id, genomes);
        });

        $('.preview-breeding').click(function(e) {
            e.preventDefault();
            var paramaters = "sire="+$("#father").val()+"&dam="+$("#mother").val();
            paramaters += "&min="+$("#size_min").val()+"&max="+$("#size_max").val();
            paramaters += "&twin="+$("#twin_chance").val()+"&depth="+$("#twin_depth").val();
            paramaters += "&chimera="+$("#chimera_chance").val()+"&genomes="+$("#chimera_depth").val();
            makeRequest("{{ url('admin/genetics/preview-breeding') }}?"+paramaters, $('.child-genomes'));
        });

        function makeRequest(url, element) {
            $.ajax({
                type: "GET", url: url, dataType: "text"
            }).done(function(res) {
                element.html(res);
                element.find('[data-toggle="tooltip"]').tooltip();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                element.html("");
                alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        }
    });
</script>
@endsection
