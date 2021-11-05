<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#breedingPermForm"> Redeem Breeding Permission{{ $tag->data == 1 ? '' : 's' }}</a>
    <div id="breedingPermForm" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This will grant the selected character {{ $tag->data }} breeding permission{{ $tag->data == 1 ? '' : 's' }} for each {{ $item->name }} used. This action is not reversible. Are you sure you want to redeem {{ $tag->data == 1 ? 'this' : 'these' }} breeding permission{{ $tag->data == 1 ? '' : 's' }}?</p>
        <div class="form-group">
            {!! Form::select('breedingperm_character_id', $characterOptions, null, ['class' => 'form-control mr-2 default character-select', 'placeholder' => 'Select Character']) !!}
        </div>
        <div class="text-right">
            {!! Form::button('Open', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
