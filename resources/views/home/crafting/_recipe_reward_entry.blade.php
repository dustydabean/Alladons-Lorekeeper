{{ $reward['quantity'] }} @if(isset($reward['asset']->image_url))<img class="" style="max-width:20%;" src="{{ $reward['asset']->image_url }}">@endif<span>{!! $reward['asset']->displayName !!}</span>
