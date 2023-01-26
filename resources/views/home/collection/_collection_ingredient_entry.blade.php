@switch($ingredient->ingredient_type)
    @case('Item')
        {{ $ingredient->quantity }} @if(isset($ingredient->ingredient->image_url))<img class="small-icon" src="{{ $ingredient->ingredient->image_url }}">@endif<span>{!! $ingredient->ingredient->displayName !!}</span>
        @break
@endswitch