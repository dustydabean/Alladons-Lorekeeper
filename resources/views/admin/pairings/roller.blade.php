@extends('admin.layout')

@section('admin-title')
    Pairing Roller
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Pairing Roller' => 'admin/pairings/roller']) !!}

    <h1>
        Pairing Roller
    </h1>

    <div class="alert alert-info">
        <strong>NOTE:</strong> ENSURE that rarities have been "sorted" in order for the rarities to correctly be chosen.
    </div>

    <p>
        Here, you can test the pairing logic by rolling MYO slots for two different characters. All pairing and boost items
        are available to test. This roller does not actually create a MYO slot,
        rather it shows the results of it.
    </p>
    <div id="characters" class="mb-3">
        @if (isset($pairing_character_1))
            @include('widgets._character_select_entry', ['character' => $pairing_character_1])
        @endif
        @if (isset($pairing_character_2))
            @include('widgets._character_select_entry', ['character' => $pairing_character_2])
        @endif
    </div>

    {!! Form::open(['url' => url()->current(), 'id' => 'pairingForm']) !!}
    <div id="characterComponents" class="row justify-content-center">
        <div class="submission-character m-3 card col-md" id="character_1">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 align-items-stretch d-flex">
                        <div class="d-flex text-center align-items-center">
                            <div class="character-image-blank">Select character.</div>
                            <div class="character-image-loaded hide"></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <a href="#" class="float-right fas fa-close"></a>
                        <div class="form-group">
                            {!! Form::label('character_codes', 'First Character') !!}
                            {!! Form::select('character_codes[]', $characters, $character[0] ?? null, ['class' => 'form-control selectize character-code', 'placeholder' => 'Select Character']) !!}
                        </div>
                    </div>
                </div>
                <div class="character-image-colours row ml-3"></div>
            </div>
        </div>
        <div class="submission-character m-3 card col-md" id="character_2">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 align-items-stretch d-flex">
                        <div class="d-flex text-center align-items-center">
                            <div class="character-image-blank">Enter character code.</div>
                            <div class="character-image-loaded hide"></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <a href="#" class="float-right fas fa-close"></a>
                        <div class="form-group">
                            {!! Form::label('character_codes', 'Second Character') !!}
                            {!! Form::select('character_codes[]', $characters, $character[1] ?? null, ['class' => 'form-control selectize character-code', 'placeholder' => 'Select Character']) !!}
                        </div>
                    </div>
                </div>
                <div class="character-image-colours row ml-3"></div>
            </div>
        </div>
    </div>

    <div class="alert hide mb-3" id="compatibility-check"></div>
    <div class="hide mb-3" id="colour-palettes"></div>

    <div class="row">
        <div class="col-md-6">
            <h2>Pairing Item</h2>
            <p>
                Decide which pairing item to use.
            </p>
            {!! Form::select('pairing_item_id[]', $pairing_items, $pairing_item_id ?? null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
        </div>
        <div class="col-md-6">
            <h2>Boost Items</h2>
            <p>
                Decide which boost items to use. Boost items are optional.
            <div class="text-right mb-3">
                <a href="#" class="btn btn-outline-info" id="addItem">Add Items</a>
            </div>
            </p>
            <table class="table table-sm" id="traitTable">
                <tbody id="itemTableBody">
                    <tr class="loot-row hide">
                        <td class="loot-row-select">
                            {!! Form::select('boost_item_ids[]', $boost_items, null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
                        </td>
                        <td class="text-right"><a href="#" class="btn btn-danger remove-trait-button">Remove</a></td>
                    </tr>
                    @if (isset($boost_item_ids) && count($boost_item_ids) > 0)
                        @foreach ($boost_item_ids as $id)
                            <tr class="loot-row">
                                <td class="loot-row-select">
                                    {!! Form::select('boost_item_ids[]', $boost_items, $id, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
                                </td>
                                <td class="text-right"><a href="#" class="btn btn-danger remove-trait-button">Remove</a></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-right">
        <a href="#" class="btn btn-success" id="pairingSubmit">Roll!</a>
    </div>

    {!! Form::close() !!}

    <div class="mt-3" id="results"></div>

@endsection
@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            let $char_1 = $('#character_1');
            let $char_2 = $('#character_2');

            attachListeners($char_1);
            attachListeners($char_2);

            let $pairingForm = $('#pairingForm');
            let $pairingSubmit = $('#pairingSubmit');
            let $results = $('#results');

            $pairingSubmit.on('click', function(e) {
                // ajax function
                e.preventDefault();
                let data = $pairingForm.serialize();
                $results.html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i></div>');
                $.ajax({
                    url: '{{ url('admin/pairings/roller') }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        $results.html(response);
                    },
                    error: function(response) {
                        $results.html(response);
                    }
                });
            });

            function attachListeners(node) {
                node.find('.character-code').on('change', function(e) {
                    let $parent = $(this).parent().parent().parent().parent();
                    $parent.find('.character-image-loaded').load('{{ url('submissions/new/character') }}/' + $(this).val(),
                        function(response, status, xhr) {
                            $parent.find('.character-image-blank').addClass('hide');
                            $parent.find('.character-image-loaded').removeClass('hide');
                            $parent.find('.character-rewards').removeClass('hide');
                        });
                    // load colour palette
                    if ("{{ config('lorekeeper.character_pairing.colours') }}" == 1) {
                        $parent.find('.character-image-colours').load('{{ url('character') }}/' + $(this).val() + '/image/colours',
                            function(response, status, xhr) {
                                $parent.find('.character-image-colours').html(response);
                            });
                    }
                    // check if both parents are selected
                    if ($char_1.find('.character-code').val() && $char_2.find('.character-code').val()) {
                        // get request to check if they are compatible
                        $.ajax({
                            url: '{{ url('characters/pairings/check') }}',
                            data: {
                                character_codes: [$char_1.find('.character-code').val(), $char_2.find('.character-code').val()]
                            },
                            success: function(data) {
                                let compatiblity_check = $('#compatibility-check')
                                let colour_palettes = $('#colour-palettes')
                                compatiblity_check.html(data.message)
                                if (data.status == 'success') {
                                    compatiblity_check.removeClass('alert-danger')
                                    compatiblity_check.addClass('alert-success')
                                } else {
                                    compatiblity_check.addClass('alert-danger')
                                    compatiblity_check.removeClass('alert-success')
                                }
                                compatiblity_check.removeClass('hide')
                                colour_palettes.html('');

                                if ("{{ config('lorekeeper.character_pairing.inherit_colours') }}" == 1 && data.palettes) {
                                    let palette_html = '<h2>Possible Colour Palette(s)</h2><div class="row justify-content-center">'
                                    for (let i = 0; i < data.palettes.length; i++) {
                                        let palette = data.palettes[i];
                                        palette_html += '<div class="col text-center"><h5>Colour Palette ' + (i + 1) + '</h5>' + palette + '</div>';
                                    }
                                    palette_html += '</div><hr class="my-3">';
                                    colour_palettes.html(palette_html);
                                    colour_palettes.removeClass('hide');
                                }
                            }
                        });
                    }
                });
            }

            var $itemTable = $('#itemTableBody');
            var $itemRow = $('#itemTableBody').find('.hide');

            $('#itemTableBody .selectize').selectize();
            attachRemoveListener($('#itemTableBody .remove-trait-button'));

            $('#addItem').on('click', function(e) {
                e.preventDefault();
                var $clone = $itemRow.clone();
                $clone.removeClass('hide');

                $itemTable.append($clone);
                attachRemoveListener($clone.find('.remove-trait-button'));
            });


            function attachRemoveListener(node) {
                node.on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            }
        });
        $('.selectize').selectize();
    </script>
@endsection
