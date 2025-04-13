<p>
    Enter the quantity of breeding permissions that this item will grant to the character selected by the user when using this item from their inventory.
</p>

<div class="form-group">
    {!! Form::label('Quantity') !!}
    {!! Form::number('quantity', $tag->data, ['class' => 'form-control']) !!}
</div>
