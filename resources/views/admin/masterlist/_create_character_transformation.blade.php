{!! Form::label('Transformation (Optional)') !!} @if ($isMyo)
    {!! add_help('This will lock the slot into a particular transformation. Leave it blank if you would like to give the user a choice, or not select a transformation.') !!}
@endif
{!! Form::select('transformation_id', $transformations, old('transformation_id'), ['class' => 'form-control', 'id' => 'transformation']) !!}
