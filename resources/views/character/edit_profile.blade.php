@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    Editing {{ $character->fullName }}'s Profile
@endsection

@section('meta-img')
    {{ $character->image->content_warnings ? asset('images/content-warning.png') : $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Editing Profile' => $character->url . '/profile/edit']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Editing Profile' => $character->url . '/profile/edit',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    @if ($character->user_id != Auth::user()->id)
        <div class="alert alert-warning">
            You are editing this character as a staff member.
        </div>
    @endif

    {!! Form::open(['url' => $character->url . '/profile/edit']) !!}
    @if (!$character->is_myo_slot)
        <div class="form-group">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', $character->name, ['class' => 'form-control']) !!}
        </div>
        @if (config('lorekeeper.extensions.character_TH_profile_link'))
            <div class="form-group">
                {!! Form::label('link', 'Profile Link') !!}
                {!! Form::text('link', $character->profile->link, ['class' => 'form-control']) !!}
            </div>
        @endif


    {!! Form::label('custom_values', "Custom Values") !!}
    <div id="customValues">
        @foreach ($character->profile->custom_values as $value)
            <div class="form-row">
                <div class="col-2 col-md-1 mb-2">
                    <span class="btn btn-link drag-custom-value-row w-100"><i class="fas fa-arrows-alt-v"></i></span>
                </div>
                <div class="col-5 col-md-3 mb-2">
                    {!! Form::text('custom_values_group[]', $value->group, ['class' => 'form-control', 'maxLength' => 50, 'placeholder' => "Group (Optional)"]) !!}
                </div>
                <div class="col-5 col-md-3 mb-2">
                    {!! Form::text('custom_values_name[]', $value->name, ['class' => 'form-control', 'maxLength' => 50, 'placeholder' => "Title:"]) !!}
                </div>
                <div class="col-10 col-md-4 mb-3">
                    {!! Form::text('custom_values_data[]', $value->data_parsed, ['class' => 'form-control', 'maxLength' => 150, 'placeholder' => "Custom Value"]) !!}
                </div>
                <div class="col-2 col-md-1 mb-3">
                    <button class="btn btn-danger delete-custom-value-row w-100" type="button">x</button>
                </div>
            </div>
        @endforeach
    </div>
    <a href="#" class="add-custom-value-row btn btn-primary mb-3">Add Custom Value</a>
@endif

<div class="form-group">
    {!! Form::label('text', 'Profile Content') !!}
    {!! Form::textarea('text', $character->profile->text, ['class' => 'wysiwyg form-control']) !!}
</div>

    @if ($character->user_id == Auth::user()->id)
        @if (!$character->is_myo_slot)
            <div class="row">
                <div class="col-md form-group">
                    {!! Form::label('is_gift_art_allowed', 'Allow Gift Art', ['class' => 'form-check-label mb-3']) !!} {!! add_help('This will place the character on the list of characters that can be drawn for gift art. This does not have any other functionality, but allow users looking for characters to draw to find your character easily.') !!}
                    {!! Form::select('is_gift_art_allowed', [0 => 'No', 1 => 'Yes', 2 => 'Ask First'], $character->is_gift_art_allowed, ['class' => 'form-control user-select']) !!}
                </div>
                <div class="col-md form-group">
                    {!! Form::label('is_gift_writing_allowed', 'Allow Gift Writing', ['class' => 'form-check-label mb-3']) !!} {!! add_help(
                        'This will place the character on the list of characters that can be written about for gift writing. This does not have any other functionality, but allow users looking for characters to write about to find your character easily.',
                    ) !!}
                    {!! Form::select('is_gift_writing_allowed', [0 => 'No', 1 => 'Yes', 2 => 'Ask First'], $character->is_gift_writing_allowed, ['class' => 'form-control user-select']) !!}
                </div>
            </div>
            <div class="col-md form-group">
                {!! Form::label('is_links_open', 'Allow Link Requests?', ['class' => 'form-check-label mb-3']) !!} {!! add_help('This will allow users to request links with your character.') !!}
                {!! Form::checkbox('is_links_open', 1, $character->is_links_open, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            </div>
        @endif
        @if ($character->is_tradeable || $character->is_sellable)
            <div class="form-group disabled">
                {!! Form::checkbox('is_trading', 1, $character->is_trading, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_trading', 'Up For Trade', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will place the character on the list of characters that are currently up for trade. This does not have any other functionality, but allow users looking for trades to find your character easily.') !!}
            </div>
        @else
            <div class="alert alert-secondary">Cannot be set to "Up for Trade" as character cannot be traded or sold.</div>
        @endif
    @endif
    @if ($character->user_id != Auth::user()->id)
        <div class="form-group">
            {!! Form::checkbox('alert_user', 1, true, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-onstyle' => 'danger']) !!}
            {!! Form::label('alert_user', 'Notify User', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will send a notification to the user that their character profile has been edited. A notification will not be sent if the character is not visible.') !!}
        </div>
    @endif
    <div class="text-right">
        {!! Form::submit('Edit Profile', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    {{-- Custom Value Row --}}
    <div class="form-row hide custom-value-row">
        <div class="col-2 col-md-1 mb-2">
            <span class="btn btn-link drag-custom-value-row w-100"><i class="fas fa-arrows-alt-v"></i></span>
        </div>
        <div class="col-5 col-md-3 mb-2">
            {!! Form::text('custom_values_group[]', null, ['class' => 'form-control', 'maxLength' => 50, 'placeholder' => "Group (Optional)"]) !!}
        </div>
        <div class="col-5 col-md-3 mb-2">
            {!! Form::text('custom_values_name[]', null, ['class' => 'form-control', 'maxLength' => 50, 'placeholder' => "Title:"]) !!}
        </div>
        <div class="col-10 col-md-4 mb-3">
            {!! Form::text('custom_values_data[]', null, ['class' => 'form-control', 'maxLength' => 150, 'placeholder' => "Custom Value"]) !!}
        </div>
        <div class="col-2 col-md-1 mb-3">
            <button class="btn btn-danger delete-custom-value-row w-100" type="button">x</button>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function(){
            $values = $("#customValues");
            $values.sortable({ handle: ".drag-custom-value-row" });
            $values.find(".delete-custom-value-row").each(function(i) {
                deleteRowOnClick($(this));
            });
            $(".add-custom-value-row").on('click', function(e) {
                e.preventDefault();
                $clone = $(".custom-value-row").clone();
                $clone.removeClass("hide custom-value-row");
                deleteRowOnClick($clone.find('.delete-custom-value-row'));
                $values.append($clone);
            });
            function deleteRowOnClick(node) {
                node.on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            }
        });
    </script>
@endsection {{-- end scripts --}}
