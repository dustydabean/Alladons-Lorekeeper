{!! Form::open(['url' => 'admin/' . ($isMyo ? 'myo/' . $character->id : 'character/' . $character->slug) . '/lineage']) !!}
<div class="alert alert-warning">
    Custom ancestor names are only used when there is no live character ID set for that ancestor.
    If there is no ancestor, leave it blank.
    <br />Ancestor names and "unknown"s will be generated automatically.
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('parent_1_id', 'Parent') !!}
            {!! Form::select('parent_1_id', $characterOptions, $character->lineage ? $character->lineage->parent_1_id : null, ['class' => 'form-control character-select', 'placeholder' => 'Unknown']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('father_name', 'Father Name') !!} {!! add_help('If the parent is known, leave this blank.') !!}
            {!! Form::text('father_name', $character->lineage ? $character->lineage->parent_1_name : null, ['class' => 'form-control', 'placeholder' => 'Unknown']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        {!! Form::label('parent_2_id', 'Parent') !!}
        {!! Form::select('parent_2_id', $characterOptions, $character->lineage ? $character->lineage->parent_2_id : null, ['class' => 'form-control character-select', 'placeholder' => 'Unknown']) !!}
    </div>
    <div class="col-md-6">
        {!! Form::label('parent_2_name', 'Partner Name') !!} {!! add_help('If the parent is known, leave this blank.') !!}
        {!! Form::text('parent_2_name', $character->lineage ? $character->lineage->parent_2_name : null, ['class' => 'form-control', 'placeholder' => 'Unknown']) !!}
    </div>
</div>

@if (false)
    {{-- collapse for custom ancestry --}}
    <div class="card my-3">
        <div class="card-header" data-toggle="collapse" data-target="#customAncestry" aria-expanded="false" aria-controls="customAncestry">
            <h2 class="h3">
                <i class="fas fa-chevron-down"></i> Custom Ancestry
            </h2>
        </div>
        <div class="collapse" id="customAncestry">
            <div class="card card-body">
                <div class="alert alert-info">
                    Custom ancestry is used to assign characters grandparents etc. when the parents should remain unknown. This is useful for characters who are not related to any other characters, but still need to be assigned a lineage.
                    Relational ancestry will always take precedent over custom ancestry, when available.
                    <br><br>
                    <strong>NOTE:</strong> You may only create custom ancestry up to the lineage_depth config option.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::label('ancestor_id', 'Ancestor') !!}
                        {!! Form::select('ancestor_id', $characterOptions, null, ['class' => 'form-control character-select', 'placeholder' => 'Unknown']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::label('ancestor_name', 'Ancestor Name') !!}
                        {!! Form::text('ancestor_name', null, ['class' => 'form-control', 'placeholder' => 'Unknown']) !!}
                    </div>
                </div>
                {{-- ancestor depth form field --}}
                <div class="form-group">
                    {!! Form::label('ancestor_depth', 'Ancestor Depth') !!}
                    {!! Form::number('ancestor_depth', 1, ['class' => 'form-control', 'min' => 1, 'max' => config('lorekeeper.lineage.lineage_depth') - 1]) !!}
                    <p>
                        Ancestry depth determines the depth of the ancestor. For example, if you want to assign a grandparent, the depth would be 1.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="text-right">
    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $('.character-select').selectize();
    });
</script>
