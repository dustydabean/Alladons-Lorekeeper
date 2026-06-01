@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Companions
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Companions' => $character->url . '/pets']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Companions' => $character->url . '/pets',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    <h1>Companions</h1>

    @if (Auth::check() && (Auth::user()->id == $character->user_id || Auth::user()->hasPower('manage_characters')))
        <p>
            Currently {{ config('lorekeeper.pets.display_pet_count') }} companion{{ config('lorekeeper.pets.display_pet_count') != 1 ? 's' : '' }} are displayed on the character's page.
            @if (config('lorekeeper.pets.max_pets') && config('lorekeeper.pets.max_pets') > 0)
                A maximum of {{ config('lorekeeper.pets.max_pets') }} companion{{ config('lorekeeper.pets.max_pets') != 1 ? 's' : '' }} can be attached.
            @endif
            <br />You can determine which companions are displayed by dragging and dropping them in the order you want.
        </p>

        {!! Form::open(['url' => 'characters/' . $character->slug . '/pets/sort', 'class' => 'text-right']) !!}
        {!! Form::hidden('sort', null, ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    @endif

    <div id="sortable" class="row sortable justify-content-center">
        @foreach ($character->pets()->orderBy('sort', 'DESC')->get() as $pet)
            <div class="col-md-3 col-6 mb-3" data-id="{{ $pet->id }}">
                <div class="card inventory-category h-100" data-id="{{ $pet->id }}">
                    <div class="card-body inventory-body text-center">
                        <div class="mb-1">
                            <a href="{{ $pet->pageUrl() }}" class="inventory-stack">
                                <img src="{{ $pet->pet->image($pet->id) }}" class="rounded img-fluid" />
                            </a>
                        </div>
                        <div>
                            @if ($pet->pet_name)
                                <a href="{{ $pet->pageUrl() }}">
                                    <div class="text-light btn btn-dark">{!! $pet->pet_name !!}</div>
                                </a>
                            @endif
                            <h5 class="mb-0">
                                {!! $pet->pet->displayName !!}
                            </h5>
                        </div>
                        <div class="mb-2 mb-md-0">
                            <div class="font-weight-bold">
                                Level {{ $pet->level->levelName ?? 1 }}
                            </div>
                            <div class="small">
                                Will level up {!! pretty_date($pet->level->levelsAt) !!}.
                            </div>
                            <div class="small" style="opacity: 0.65;">
                                (<b>{{ $pet->level->bonding }} EXP</b>, minus {{ $pet->level->bonding > 0 ? $pet->level->bonding * 7 : 0 }} days)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // when form is submitted disable button and hide form
            $('#bondForm').submit(function(e) {
                e.preventDefault();
                $('#bond').prop('disabled', true);
                $('#bondForm').hide();

                // submit form
                e.target.submit();
            });

            $("#sortable").sortable({
                characters: '.sort-item',
                placeholder: "sortable-placeholder col-md-3 col-6",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
            });
            $("#sortable").disableSelection();
        });
    </script>
@endsection
