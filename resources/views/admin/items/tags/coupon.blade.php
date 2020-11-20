<h3>Coupon</h3>

<p>Select the shops this coupon should allowed to be used in.</p>

<h3>Shops</h3>
    <div class="form-group">
        {!! Form::label('Name') !!} {!! add_help('Enter a descriptive name for the type of character this slot can create, e.g. Rare MYO Slot. This will be listed on the MYO slot masterlist.') !!}
        {!! Form::select('name', $tag->getData(), null, ['shop'], ['class' => 'form-control', 'placeholder' => 'Select Shop']) !!}
    </div>