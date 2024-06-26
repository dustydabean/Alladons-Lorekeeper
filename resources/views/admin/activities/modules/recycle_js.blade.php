@include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])
<script>
$( document ).ready(function() {
    var $table  = $('#recyclable');
    var $row = $('#recyclableRow').find('.recyclable-row');
    var $itemSelect = $('#recycleRowData').find('.item-select');
    var $categorySelect = $('#recycleRowData').find('.category-select');

    $('#recyclable .selectize').selectize();
    attachRemoveListener($('#recyclable .remove-recyclable-button'));

    $('#addRecyclable').on('click', function(e) {
        console.log('here');
        e.preventDefault();
        var $clone = $row.clone();
        $table.append($clone);
        attachRewardTypeListener($clone.find('.recyclable-type'));
        attachRemoveListener($clone.find('.remove-recyclable-button'));
    });

    $('.recyclable-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.recyclable-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'ItemCategory') $clone = $categorySelect.clone();

        $cell.html('');
        $cell.append($clone);
    });

    function attachRewardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.recyclable-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'ItemCategory') $clone = $categorySelect.clone();

            $cell.html('');
            $cell.append($clone);
            if (val != 'ItemCategoryRarity' && val != 'ItemRarity') $clone.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    }
});

</script>