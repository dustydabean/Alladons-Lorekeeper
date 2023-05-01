<img src="/images/avatars/{{ $user->avatar }}" class="float-left rounded-circle mr-3" style="width:100px; height:100px;" alt="{{ $user->name }}">
<div class="card">
	<h5 class="card-header">
	    {!! $user->displayName !!}
	</h5>
	<div class="card-body">
		{!! isset($user->staffProfile->text) ? $user->staffProfile->text : '<i>No profile found.</i>' !!}

		@if(isset($user->staffProfile->contacts))
			<br>
			<div class="row">
				@foreach($user->staffProfile->contacts['site'] as $key=>$contact)
					<div class="col-12 col-sm-6 p-0 px-md-3">
						<a href="{{ $user->staffProfile->contacts['url'][$key] }}"><i class="fas fa-link small"></i> {{ $contact }}</a>
					</div>
				@endforeach
			</div>
		@else
			<br>
			<i>Contact me <a href="{{ $user->url }}">on-site</a> only.</i>
		@endif
	</div>
</div>