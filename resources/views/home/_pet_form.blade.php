<li class="list-group-item">
    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#nameForm">
        @if ($pet->user_id != Auth::user()->id)
            [ADMIN]
        @endif Name Companion
    </a>
    {!! Form::open(['url' => 'pets/name/' . $pet->id, 'id' => 'nameForm', 'class' => 'collapse']) !!}
    <p>Enter a name to display for the companion!</p>
    <div class="form-group">
        {!! Form::label('name', 'Name') !!} {!! add_help('If your name is not appropriate you can be banned.') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="text-right">
        {!! Form::submit('Name', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
</li>

<li class="list-group-item">
    <a class="card-title h5 collapse-title" data-toggle="collapse" href="#descForm">
        @if ($pet->user_id != Auth::user()->id)
            [ADMIN]
        @endif Edit Profile
    </a>
    {!! Form::open(['url' => 'pets/description/' . $pet->id, 'id' => 'descForm', 'class' => 'collapse']) !!}
    <p>Tell everyone about your companion.</p>
    <div class="form-group">
        {!! Form::label('Profile Text (Optional)') !!}
        {!! Form::textarea('description', $pet->description, ['class' => 'form-control wysiwyg']) !!}
    </div>
    <div class="text-right">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
</li>

@if (!$pet->pet->category || ($pet->pet->category->allow_attach && (!isset($pet->pet->category->limit) || $pet->pet->category->limit > 0)))
    <li class="list-group-item">
        @php
            $now = Carbon\Carbon::parse($pet->attached_at);
            $diff = $now->addDays(Settings::get('claymore_cooldown'));
        @endphp
        @if ($pet->character_id != null && $diff < Carbon\Carbon::now())
            <a class="card-title h5 collapse-title" data-toggle="collapse" href="#attachForm">
                @if ($pet->user_id != $user->id)
                    [ADMIN]
                @endif Detach Companion from Character
            </a>
            {!! Form::open(['url' => 'pets/detach/' . $pet->id, 'id' => 'attachForm', 'class' => 'collapse']) !!}
            <p>This companion is currently attached to {!! $pet->character->displayName !!}, do you want to detach them?</p>
            <div class="text-right">
                {!! Form::submit('Detach', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        @elseif($pet->character_id == null || $diff < Carbon\Carbon::now())
            <a class="card-title h5 collapse-title" data-toggle="collapse" href="#attachForm">
                @if ($pet->user_id != $user->id)
                    [ADMIN]
                @endif Attach Companion to Character
            </a>
            {!! Form::open(['url' => 'pets/attach/' . $pet->id, 'id' => 'attachForm', 'class' => 'collapse']) !!}
            <p>Attach this companion to a character you own! They'll appear on the character's page and any stat bonuses will automatically be applied.</p>
            <p>Companions can be detached.</p>
            <div class="form-group">
                {!! Form::label('id', 'Slug') !!} {!! add_help('Insert your character\'s slug.') !!}
                {!! Form::select(
                    'id',
                    $pet->user->characters()->myo()->pluck('slug', 'id'),
                    null,
                    ['class' => 'form-control'],
                ) !!}
            </div>
            <div class="text-right">
                {!! Form::submit('Attach', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        @else
            <a class="card-title h5">You cannot currently attach / detach this companion! It is under cooldown.</a>
        @endif
    </li>
@endif

@if ($user && isset($splices) && count($splices) && $user->id == $pet->user_id)
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#userVariantForm">Change Companion Variant</a>
        {!! Form::open(['url' => 'pets/variant/' . $pet->id, 'id' => 'userVariantForm', 'class' => 'collapse']) !!}
        <p>
            This will use a splice item!
            @if ($pet->pet->isVariant)
                <br>Current Variant: {{ $pet->pet->name }}
            @endif
        </p>
        <div class="form-group">
            {!! Form::select('stack_id', $splices, null, ['class' => 'form-control splice-item-select', 'placeholder' => 'Select Splice Item']) !!}
        </div>
        <div class="form-group splice-item-dropdown">
            {!! Form::select('variant_id', [], null, ['class' => 'form-control', 'placeholder' => 'Select a splice item first...', 'disabled']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Change Variant', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </li>

    <script>
        $(".splice-item-select").change(function() {
            var $stack = $('.splice-item-select').val();

            if ($stack.length) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('pets/variant-check') }}/" + $stack + "/" + {{ $pet->id }},
                    dataType: "text"
                }).done(function(res) {
                    $(".splice-item-dropdown").html(res);
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert("AJAX call failed: " + textStatus + ", " + errorThrown);
                });
            }
        });
    </script>
@endif

@if ($user->hasPower('edit_inventories'))
    {{-- variant --}}
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#variantForm">[ADMIN] Change Companion Variant</a>
        {!! Form::open(['url' => 'pets/variant/' . $pet->id, 'id' => 'variantForm', 'class' => 'collapse']) !!}
        {!! Form::hidden('is_staff', 1) !!}
        <div class="form-group">
            @php
                $variants =
                    ['0' => 'Default'] +
                    ($pet->pet->isVariant
                        ? $pet->pet->parent
                            ->variants()
                            ->pluck('name', 'id')
                            ->toArray()
                        : $pet->pet
                            ->variants()
                            ->pluck('name', 'id')
                            ->toArray());
            @endphp
            {!! Form::select('variant_id', $variants, $pet->pet->isVariant ? $pet->pet_id : 0, ['class' => 'form-control mt-2']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Change Variant', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </li>

    {{-- evolution --}}
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#evolutionForm">[ADMIN] Change Companion Evolution</a>
        {!! Form::open(['url' => 'pets/evolution/' . $pet->id, 'id' => 'evolutionForm', 'class' => 'collapse']) !!}
        {!! Form::hidden('is_staff', 1) !!}
        <div class="form-group">
            @php
                $evolutions =
                    ['0' => 'Default'] +
                    $pet->pet
                        ->evolutions()
                        ->pluck('evolution_name', 'id')
                        ->toArray();
            @endphp
            {!! Form::select('evolution_id', $evolutions, $pet->evolution_id, ['class' => 'form-control mt-2']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Change Evolution', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </li>

    {{-- custom image --}}
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#imageForm">[ADMIN] Change Image</a>
        {!! Form::open(['url' => 'pets/image/' . $pet->id, 'id' => 'imageForm', 'class' => 'collapse', 'files' => true]) !!}
        <div class="form-group mt-2">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
            <div class="text-muted">Recommended size: 100px x 100px</div>
            @if ($pet->has_image)
                <div class="form-check">
                    {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
        <div class="col-md">
            {!! Form::label('Companion Artist (Optional)') !!} {!! add_help('Provide the artist\'s username if they are on site or, failing that, a link.') !!}
            <div class="row">
                <div class="col-md">
                    <div class="form-group">
                        {!! Form::select('artist_id', $userOptions, $pet->artist_id ? $pet->artist_id : null, ['class' => 'form-control mr-2 selectize']) !!}
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        {!! Form::text('artist_url', $pet->artist_url ? $pet->artist_url : '', [
                            'class' => 'form-control mr-2',
                            'placeholder' => 'Artist URL',
                        ]) !!}
                    </div>
                </div>
            </div>
            @if ($pet->has_image)
                <div class="form-check">
                    {!! Form::checkbox('remove_credit', 1, false, ['class' => 'form-check-input']) !!}
                    {!! Form::label('remove_credit', 'Remove current credits', ['class' => 'form-check-label']) !!}
                </div>
            @endif
        </div>
        <div class="text-right">
            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </li>

    {{-- adjust bonding --}}
    <li class="list-group-item">
        <a class="card-title h5 collapse-title" data-toggle="collapse" href="#bondingForm">
            [ADMIN] Adjust EXP
        </a>
        {!! Form::open(['url' => 'pets/edit-exp/' . $pet->id, 'id' => 'bondingForm', 'class' => 'collapse']) !!}
        <p>
            Adjust the companion's EXP value. <u>1 EXP is the equivalent of one week of time subtracted from gaining their next level.</u> Enter a positive number to add or a negative number to subtract.
            @if ($pet->level)
                <br><strong>Current EXP:</strong> {{ $pet->level->bonding }}
            @else
                <br><strong>Current EXP:</strong> 0
            @endif
        </p>
        <div class="form-group">
            {!! Form::label('bonding_amount', 'Bonding Amount') !!} {!! add_help('Enter an integer value. Positive values add EXP, negative values subtract. The companion\'s EXP cannot go below 0.') !!}
            {!! Form::number('bonding_amount', null, ['class' => 'form-control', 'placeholder' => 'e.g. 5 or -3']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Adjust EXP', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </li>
@endif
