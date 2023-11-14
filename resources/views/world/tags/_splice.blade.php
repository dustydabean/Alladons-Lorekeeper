@if ($tag->getData()['variant_ids'])
    <div class="container p-2">
        <b class="ml-2">Limited to:</b> {!! $tag->getData()['display'] !!}
    </div>
@endif
