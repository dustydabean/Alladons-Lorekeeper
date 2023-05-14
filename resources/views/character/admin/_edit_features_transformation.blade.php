{!! Form::label('Transformation (Optional)') !!}
{!! Form::select('transformation_id', $transformations, $image->transformation_id, ['class' => 'form-control', 'id' => 'transformation']) !!}
