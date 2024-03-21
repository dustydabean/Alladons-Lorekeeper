<div class="col-lg-4 p-2">
    <div class="card character-bio w-100 p-3">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-4">
                <h5>Species</h5>
            </div>
            <div class="col-lg-8 col-md-6 col-8">{!! $myo['species'] !!}</div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-4">
                <h5>Subtype</h5>
            </div>
            <div class="col-lg-8 col-md-6 col-8">{!! $myo['subtype'] !!}</div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-4">
                <h5>Rarity</h5>
            </div>
            <div class="col-lg-8 col-md-6 col-8">{!! $myo['rarity'] !!}</div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-4">
                <h5>Sex</h5>
            </div>
            <div class="col-lg-8 col-md-6 col-8">{!! $myo['sex'] !!}</div>
        </div>
        @if (config('lorekeeper.character_pairing.colours'))
            <div class="row">
                <div class="col-lg-4 col-md-6 col-4">
                    <h5>Colours</h5>
                </div>
                <div class="col-lg-8 col-md-6 col-8">
                    {!! $myo['palette'] ?? '<div class="alert alert-info">No colours generated. Inheritance likely disabled.</div>' !!}
                </div>
            </div>
        @endif
        <div class="mb-3">
            <div>
                <h5>Traits</h5>
            </div>

            <div>
                @if (count($myo['features']) > 0)
                    @foreach ($myo['features'] as $feature)
                        <div>
                            <strong>{!! $feature->displayName !!}:</strong>
                            ({!! $feature->rarity->displayName !!})
                            {{ isset($myo['feature_data'][$loop->index]) ? '(' . $myo['feature_data'][$loop->index] . ')' : '' }}
                        </div>
                    @endforeach
                @else
                    <div>No traits listed.</div>
                @endif
            </div>
        </div>
    </div>
</div>
