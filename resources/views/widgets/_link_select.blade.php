@php
    $characters = \App\Models\Character\Character::visible(Auth::check() ? Auth::user() : null)
        ->whereNotIn(
            'id',
            $character->links
                ->pluck('character_1_id')
                ->merge($character->links->pluck('character_2_id'))
                ->toArray(),
        )
        ->where('id', '!=', $character->id)
        ->myo(0)
        ->orderBy('slug', 'DESC')
        ->get()
        ->pluck('fullName', 'slug')
        ->toArray();
    $tables = \App\Models\Loot\LootTable::orderBy('name')->pluck('name', 'id');
@endphp

<div id="characterComponents" class="hide">
    <div class="submission-character mb-3 card">
        <div class="card-body">
            <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
            <div class="row">
                <div class="col-md-2 align-items-stretch d-flex">
                    <div class="d-flex text-center align-items-center">
                        <div class="character-image-blank">Select character code.</div>
                        <div class="character-image-loaded hide"></div>
                    </div>
                </div>
                <div class="col-md-10">
                    <a href="#" class="float-right fas fa-close"></a>
                    <div class="form-group">
                        {!! Form::label('slug[]', 'Character Code') !!}
                        {!! Form::select('slug[]', $characters, null, ['class' => 'form-control character-code', 'placeholder' => 'Select Character']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
