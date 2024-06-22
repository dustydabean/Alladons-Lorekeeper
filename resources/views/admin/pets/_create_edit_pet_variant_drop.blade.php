@if ($variants)
    {!! Form::open(['url' => 'admin/data/pets/drops/edit/' . $pet->id . '/variants/' . ($variant_drop->id ? 'edit/' . $variant->id : 'create')]) !!}

    <!-- Should include drops for all groups and allow loot selection upon creation -->

    @if (!$variant_drop->id)
        <h3>Create Drop for {!! $pet->displayName !!}</h3>
        <div class="form-group">
            {!! Form::label('variant_id', 'Variant') !!}
            {!! Form::select('variant_id', $variants, null, ['class' => 'form-control', 'placeholder' => 'Select Variant']) !!}
        </div>
    @else
        <h3>Edit Drop for {!! $variant->displayName !!}</h3>
    @endif

    <h4>Dropped Items</h4>
    <p>Select an item for each group of this pet to drop. Choose "Reward: None" to disable drops for the group.</p>
    <div class="card card-body my-2 mb-4">
        @foreach ($pet->dropData->parameters as $label => $weight)
            <div class="mb-2">
                <h5>{{ $label }}</h5>
                <div class="form-group">
                    @include('widgets._pet_drop_loot_select', [
                        'loots' => $variant_drop->rewards()[strtolower($label)] ?? null,
                        'group' => strtolower(str_replace(' ', '_', $label)),
                        'label' => $label,
                    ])
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-right">
        {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    <div class="hide">
        @include('widgets._pet_drop_loot_select_row', ['group' => ''])
    </div>
@else
    <p>No variants found.</p>
@endif

@include('js._pet_loot_js')
