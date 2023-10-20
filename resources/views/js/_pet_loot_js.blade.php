<script>
    $(document).ready(function() {
        // blank data to clone from
        var $lootTable = $('#lootTableBody');
        var $lootRow = $('#lootRow').find('.loot-row');
        var $itemSelect = $('#lootRowData').find('.item-select');
        var $currencySelect = $('#lootRowData').find('.currency-select');
        var $tableSelect = $('#lootRowData').find('.table-select');

        $('.lootTableBody .selectize').selectize();
        attachRemoveListener($('.lootTableBody .remove-loot-button'));

        $('.addLoot').on('click', function(e) {
            e.preventDefault();
            var $clone = $lootRow.clone();
            // get the group name
            var group = $(this).parent().parent().attr('data-id');
            changeFormName($clone, group);

            $(this).parent().parent().find('.lootTableBody').append($clone);
            attachRewardTypeListener($clone.find('.reward-type'));
            attachRemoveListener($clone.find('.remove-loot-button'));
        });

        function changeFormName(node, group) {
            node.find('.reward-type').attr('name', 'rewardable_type[' + group + '][]');
            node.find('.rewardable-id').attr('name', 'rewardable_id[' + group + '][]');
            node.find('.item-select').attr('name', 'rewardable_id[' + group + '][]');
            node.find('.currency-select').attr('name', 'rewardable_id[' + group + '][]');
            node.find('.table-select').attr('name', 'rewardable_id[' + group + '][]');
            node.find('.min-quantity').attr('name', 'min_quantity[' + group + '][]');
            node.find('.max-quantity').attr('name', 'max_quantity[' + group + '][]');
        }

        $('.reward-type').on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.loot-row-select');

            var $clone = null;
            if (val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'Pet') $clone = $PetSelect.clone();
            else if (val == 'LootTable') $clone = $tableSelect.clone();

            // set clone name to match the group
            var group = $(this).parent().parent().parent().parent().parent().attr('data-id');
            $clone.attr('name', 'rewardable_id[' + group + '][]');

            $cell.html('');
            $cell.append($clone);
        });

        function attachRewardTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.loot-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Currency') $clone = $currencySelect.clone();
                else if (val == 'Pet') $clone = $PetSelect.clone();
                else if (val == 'LootTable') $clone = $tableSelect.clone();

                // set clone name to match the group
                var group = $(this).parent().parent().parent().parent().parent().attr('data-id');
                $clone.attr('name', 'rewardable_id[' + group + '][]');

                $cell.html('');
                $cell.append($clone);
                $clone.selectize();
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
