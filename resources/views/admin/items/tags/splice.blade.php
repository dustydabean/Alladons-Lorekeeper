<p>
    You can choose if this splice item can be used on any pets (with variants) and all their variants, only specific pet species and all their variants, or only specific variants. While pets that are not visible will be shown here in the admin panel,
    users will not be able to view non-visible variant options in the dropdown until they are officially made visible.
</p>

<div class="form-group">
    {!! Form::label('splice_type', 'Splice Item Type:', ['class' => 'form-control-label']) !!}
    {!! Form::select(
        'splice_type',
        [
            'all' => 'Can be used on any pet for any variant',
            'by_species' => 'Can be used on specific pet(s) for all of their variants',
            'by_variants' => 'Can only be used for specified variants only',
        ],
        isset($tag->getData()['splice_type']) ? $tag->getData()['splice_type'] : null,
        ['class' => 'form-control splice-type-select'],
    ) !!}
</div>

<div class="form-group by-species {{ isset($tag->getData()['splice_type']) && $tag->getData()['splice_type'] == 'by_species' ? '' : 'hide' }}">
    {!! Form::label('Select Pet Species') !!}
    {!! Form::select('parent_ids[]', $parents, isset($tag->getData()['parent_ids']) ? $tag->getData()['parent_ids'] : null, ['class' => 'form-control selectize', 'multiple', 'placeholder' => 'Select Pets']) !!}
</div>

<div class="form-group by-variants {{ isset($tag->getData()['splice_type']) && $tag->getData()['splice_type'] == 'by_variants' ? '' : 'hide' }}">
    {!! Form::label('Select Specific Variants') !!}
    {!! Form::select('variant_ids[]', ['default' => 'Default'] + $variants, isset($tag->getData()['variant_ids']) ? $tag->getData()['variant_ids'] : null, ['class' => 'form-control selectize', 'multiple', 'placeholder' => 'Select Variants']) !!}
</div>

<script>
    $(document).ready(function() {
        $('.selectize').selectize();
        var $toggle = $('.splice-type-select');
        var $speciesRow = $('.by-species');
        var $variantRow = $('.by-variants');

        $toggle.on('change', function(e) {
            var val = $toggle.val();

            if (val == 'any') {
                if (!$speciesRow.hasClass('hide')) {
                    $speciesRow.addClass('hide');
                }
                if (!$variantRow.hasClass('hide')) {
                    $variantRow.addClass('hide');
                }
            } else if (val == 'by_species') {
                if ($speciesRow.hasClass('hide')) {
                    $speciesRow.removeClass('hide');
                }
                if (!$variantRow.hasClass('hide')) {
                    $variantRow.addClass('hide');
                }
            } else if (val == 'by_variants') {
                if (!$speciesRow.hasClass('hide')) {
                    $speciesRow.addClass('hide');
                }
                if ($variantRow.hasClass('hide')) {
                    $variantRow.removeClass('hide');
                }
            }
        });
    });
</script>
