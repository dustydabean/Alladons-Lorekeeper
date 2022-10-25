@if($character)
    <!-- character gift status badges -->
    <span class="btn {{ $character->is_gift_writing_allowed == 1 ? 'badge-success' : ($character->is_gift_writing_allowed == 2 ? 'badge-warning text-light' : 'badge-danger') }}" data-toggle="tooltip" data-placement="top" title="{{ $character->is_gift_writing_allowed == 1 ? 'OPEN for gift writing.' : ($character->is_gift_writing_allowed == 2 ? 'PLEASE ASK before gift writing.' : 'CLOSED for gift writing.') }}"><i class="fas fa-file-alt"></i></span>
    <span class="btn {{ $character->is_gift_art_allowed == 1 ? 'badge-success' : ($character->is_gift_art_allowed == 2 ? 'badge-warning text-light' : 'badge-danger') }} ml-2" data-toggle="tooltip" data-placement="top" title="{{ $character->is_gift_art_allowed == 1 ? 'OPEN for gift art.' : ($character->is_gift_art_allowed == 2 ? 'PLEASE ASK before gift art.' : 'CLOSED for gift art.') }}"><i class="fas fa-pencil-ruler"></i></span>
@endif
