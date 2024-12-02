<div class="row">
    <div class="col-lg-3 col-4">
        <h5>Owner</h5>
    </div>
    <div class="col-lg-9 col-8">{!! $character->displayOwner !!}</div>
</div>
<div class="row">
    <div class="col-lg-3 col-4">
        <h5>Rarity</h5>
    </div>
    <div class="col-lg-8 col-md-6 col-8">{!! $character->image->rarity_id ? $character->image->rarity->displayName : 'None' !!}</div>
</div>

<hr/>

<h5>
    <i class="text-{{ $character->is_giftable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_giftable ? 'Can' : 'Cannot' }} be gifted
</h5>
<h5>
    <i class="text-{{ $character->is_tradeable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_tradeable ? 'Can' : 'Cannot' }} be traded
</h5>
<h5>
    <i class="text-{{ $character->is_sellable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_sellable ? 'Can' : 'Cannot' }} be sold
</h5>
@if ($character->sale_value > 0)
    <div class="row">
        <div class="col-lg-3 col-4">
            <h5>Sale Value</h5>
        </div>
        <div class="col-lg-9 col-8">
            {{ config('lorekeeper.settings.currency_symbol') }}{{ $character->sale_value }}
        </div>
    </div>
@endif
@if ($character->transferrable_at && $character->transferrable_at->isFuture())
    <div class="row">
        <div class="col-lg-3 col-4">
            <h5>Cooldown</h5>
        </div>
        <div class="col-lg-9 col-8">Cannot be transferred until {!! format_date($character->transferrable_at) !!}</div>
    </div>
@endif
@if (Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-stats" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif
