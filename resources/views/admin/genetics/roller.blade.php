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
<div class="text-center">
    <a href="#" class="btn btn-success text-uppercase preview-breeding"><i class="fas fa-dna mr-2"></i>Preview<i class="fas fa-dna ml-2"></i></a>
</div>
<hr>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mb-3 mb-md-0">
            <div class="card-header">
                <h5 class="mb-0 text-center">Children</h5>
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
            makeRequest("{{ url('admin/genetics/preview-breeding') }}?sire="+$("#father").val()+"&dam="+$("#mother").val(), $('.child-genomes'));
        })
        function makeRequest(url, element) {
            $.ajax({
                type: "GET", url: url, dataType: "text"
            }).done(function(res) {
                element.html(res);
                element.find('[data-toggle="tooltip"]').tooltip();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                element.html("");
                //alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        }
    });
</script>
@endsection
