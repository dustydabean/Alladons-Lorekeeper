{!! Form::open(['url' => $character->url . '/links/delete/' . $link->id]) !!}
<p>You will have to make a new request to get back the link. All data is non-retrievable.</p>
<b>Are you sure you want to delete this link?</b>

<br>
{!! Form::button('<i class="fas fa-trash"></i> Delete', ['class' => 'btn btn-danger btn-sm m-1 float-right', 'type' => 'submit']) !!}
{!! Form::close() !!}
