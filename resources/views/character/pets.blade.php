@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Pets
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    @if ($character->is_myo_slot)
        {!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Pets' => $character->url . '/pets']) !!}
    @else
        {!! breadcrumbs([
            $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
            $character->fullName => $character->url,
            'Pets' => $character->url . '/pets',
        ]) !!}
    @endif

    @include('character._header', ['character' => $character])

    <h1>Pets</h1>

    @if(Auth::check() && (Auth::user()->id == $character->user_id || Auth::user()->hasPower('manage_characters')))
        <p>
            Currently {{ config('lorekeeper.pets.display_pet_count') }} pet{{ config('lorekeeper.pets.display_pet_count') != 1 ? 's' : '' }} are displayed on the character's page.
            <br />You can determine which pets are displayed by dragging and dropping them in the order you want.
        </p>
        {!! Form::open(['url' => 'characters/' . $character->slug . '/pets/sort', 'class' => 'text-right']) !!}
        {!! Form::hidden('sort', null, ['id' => 'sortableOrder']) !!}
        {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    @endif

    <div id="sortable" class="row sortable justify-content-center">
        @foreach($character->pets()->orderBy('sort', 'DESC')->get() as $pet)
            <div class="col-md-3 col-6" data-id="{{ $pet->id }}">
                <div class="card mb-3 inventory-category h-100" data-id="{{ $pet->id }}">
                    <div class="card-body inventory-body text-center">
                        <div class="mb-1">
                            <a href="{{ $pet->pageUrl() }}" class="inventory-stack">
                                <img src="{{ $pet->pet->variantImage($pet->id) }}" class="rounded img-fluid" />
                            </a>
                        </div>
                        <div>
                            @if ($pet->pet_name)
                                <a href="{{ $pet->pageUrl() }}">
                                    <div class="text-light btn btn-dark">{!! $pet->pet_name !!}</div>
                                </a>
                            @endif
                            <div>{!! $pet->pet->displayName !!}</div>
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
    