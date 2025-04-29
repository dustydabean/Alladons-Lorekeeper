@php
    $c = 1;
@endphp

@foreach ($character->breedingSlots as $slot)
    <div class="row no-gutters mb-2">
        <div class="col-lg-2 col-3">
            <h5>Slot {{ $c }}</h5>
        </div>
        <div class="col pl-1">
            {!! $slot->displayLink() !!}<span class="mx-1">||</span>{!! $slot->displayOffspring() !!} {!! $slot->displayNotes() !!}
        </div>
        @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
            <div class="col-auto text-right pl-2">
                <a href="#" class="btn btn-outline-info btn-sm edit-breeding-slot" data-id="{{ $slot->id }}">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
        @endif
    </div>
    @php
        $c++;
    @endphp
@endforeach
