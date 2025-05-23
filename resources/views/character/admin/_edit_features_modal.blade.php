{!! Form::open(['url' => 'admin/character/image/' . $image->id . '/traits']) !!}
<div class="form-group">
    {!! Form::label('Species') !!}
    {!! Form::select('species_id', $specieses, $image->species_id, ['class' => 'form-control', 'id' => 'species']) !!}
</div>

<div class="form-group" id="subtypes">
    {!! Form::label('Subtypes (Optional)') !!}
    {!! Form::select('subtype_ids[]', $subtypes, $image->subtypes()->pluck('subtype_id')->toArray() ?? [], ['class' => 'form-control', 'id' => 'subtype', 'multiple']) !!}
</div>

<div class="form-group" id="transformations">
    {!! Form::label('Ref Type (Optional)') !!}
    {!! Form::select('transformation_id', $transformations, $image->transformation_id, ['class' => 'form-control selectize', 'id' => 'transformation']) !!}
</div>

<div class="form-group">
    {!! Form::label('Nickname (Optional)') !!}
    {!! Form::text('nickname', $image->character->nickname, ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('poucher_code', 'Poucher Code') !!}
            {!! Form::text('poucher_code', $image->character->poucher_code, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Generation (Optional)') !!}
            {!! Form::select('generation_id', $generations, $image->character->generation_id, ['class' => 'form-control selectize', 'id' => 'generationSelect']) !!}
        </div>
    </div>
</div>

{!! Form::label('Pedigree Name (Optional)') !!} {!! add_help('While this is optional, if you set a pedigree tag you must set a descriptor and vice versa.') !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::select('pedigree_id', $pedigrees, $image->character->pedigree_id, ['class' => 'form-control selectize', 'id' => 'pedigreeSelect']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::text('pedigree_descriptor', $image->character->pedigree_descriptor, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Birthdate (Optional)') !!}
    <div class="input-group">
        {!! Form::text('birthdate', $image->character->birthdate, ['class' => 'form-control datepickerdob']) !!}
        <div class="input-group-append">
            <a class="btn btn-info collapsed" href="#collapseddob" data-toggle="collapse"><i class="fas fa-calendar-alt"></i></a>
        </div>
    </div>
    <div class="collapse dobpicker" id="collapseddob" style="position: relative; z-index: 9999;"></div>
</div>

<div class="form-group">
    {!! Form::label('Character Sex (Optional)') !!}
    {!! Form::select('sex', [null => 'Select Sex', 'Male' => 'Male', 'Female' => 'Female'], $image->sex, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Mutations') !!}
    <div><a href="#" class="btn btn-primary mb-2" id="add-feature">Add Mutation</a></div>
    <div id="featureList">
        @foreach ($image->features as $feature)
            <div class="d-flex mb-2">
                {!! Form::select('feature_id[]', $features, $feature->feature_id, ['class' => 'form-control mr-2 feature-select original', 'placeholder' => 'Select Mutation']) !!}
                {!! Form::text('feature_data[]', $feature->data, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
                <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
            </div>
        @endforeach
    </div>
    <div class="feature-row hide mb-2">
        {!! Form::select('feature_id[]', $features, null, ['class' => 'form-control mr-2 feature-select', 'placeholder' => 'Select Mutation']) !!}
        {!! Form::text('feature_data[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Extra Info (Optional)']) !!}
        <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
    </div>
</div>

<div class="text-right">
    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}

@include('widgets._datetimepicker_js', ['dtinline' => 'datepickeralt', 'dtvalue' => $image->character->transferrable_at, 'dobpicker' => true, 'character' => $image->character])
<script>
    $(document).ready(function() {
        @if (config('lorekeeper.extensions.organised_traits_dropdown'))
            $('.original.feature-select').selectize({
                render: {
                    item: featureSelectedRender
                }
            });
        @else
            $('.original.feature-select').selectize();
        @endif
        $('#add-feature').on('click', function(e) {
            e.preventDefault();
            addFeatureRow();
        });
        $('.remove-feature').on('click', function(e) {
            e.preventDefault();
            removeFeatureRow($(this));
        })

        $('.selectize').selectize();

        function addFeatureRow() {
            var $clone = $('.feature-row').clone();
            $('#featureList').append($clone);
            $clone.removeClass('hide feature-row');
            $clone.addClass('d-flex');
            $clone.find('.remove-feature').on('click', function(e) {
                e.preventDefault();
                removeFeatureRow($(this));
            })

            @if (config('lorekeeper.extensions.organised_traits_dropdown'))
                $clone.find('.feature-select').selectize({
                    render: {
                        item: featureSelectedRender
                    }
                });
            @else
                $clone.find('.feature-select').selectize();
            @endif
        }

        function removeFeatureRow($trigger) {
            $trigger.parent().remove();
        }

        function featureSelectedRender(item, escape) {
            return '<div><span>' + escape(item["text"].trim()) + ' (' + escape(item["optgroup"].trim()) + ')' + '</span></div>';
        }
        refreshSubtype();
    });

    $("#species").change(function() {
        refreshSubtype();
    });

    function refreshSubtype() {
        var species = $('#species').val();
        var id = '<?php echo $image->id; ?>';
        $.ajax({
            type: "GET",
            url: "{{ url('admin/character/image/traits/subtype') }}?species=" + species + "&id=" + id,
            dataType: "text"
        }).done(function(res) {
            $("#subtypes").html(res);
            $("#subtype").selectize({
                maxItems: {{ config('lorekeeper.extensions.multiple_subtype_limit') }},
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("AJAX call failed: " + textStatus + ", " + errorThrown);
        });
    };

    $("#subtype").selectize({
        maxItems: {{ config('lorekeeper.extensions.multiple_subtype_limit') }},
    });
</script>
