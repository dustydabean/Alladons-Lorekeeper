<script>
    $(document).ready(function() {
        var $lootTable = $('#lootTableBody');
        var $lootRow = $('#lootRow').find('.loot-row');
        var $itemSelect = $('#lootRowData').find('.item-select');
        var $petSelect = $('#lootRowData').find('.pet-select');
        var $petVariantSelect = $('#lootRowData').find('.pet-variant-select');
        var $currencySelect = $('#lootRowData').find('.currency-select');
        @if ($showLootTables)
            var $tableSelect = $('#lootRowData').find('.table-select');
        @endif
        @if ($showRaffles)
            var $raffleSelect = $('#lootRowData').find('.raffle-select');
        @endif
        @if (isset($showRecipes) && $showRecipes)
            var $recipeSelect = $('#lootRowData').find('.recipe-select');
        @endif
        @if (isset($showThemes) && $showThemes)
            var $themeSelect = $('#lootRowData').find('.theme-select');
        @endif

        $('#lootTableBody .selectize').selectize();
        attachRemoveListener($('#lootTableBody .remove-loot-button'));

        $('#addLoot').on('click', function(e) {
            e.preventDefault();
            var $clone = $lootRow.clone();
            $lootTable.append($clone);
            attachRewardTypeListener($clone.find('.reward-type'));
            attachRemoveListener($clone.find('.remove-loot-button'));
        });

        $('.reward-type').on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.loot-row-select');

            var $clone = null;
            if (val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'Pet') $clone = $petSelect.clone();
            else if (val == 'Pet Variant') $clone = $petVariantSelect.clone();
            @if ($showLootTables)
                else if (val == 'LootTable') $clone = $tableSelect.clone();
            @endif
            @if ($showRaffles)
                else if (val == 'Raffle') $clone = $raffleSelect.clone();
            @endif
            @if (isset($showThemes) && $showThemes)
                else if (val == 'Theme') $clone = $themeSelect.clone();
            @endif
            @if (isset($showRecipes) && $showRecipes)
                else if (val == 'Recipe') $clone = $recipeSelect.clone();
            @endif

            $cell.html('');
            $cell.append($clone);
        });

        function attachRewardTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.loot-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Pet') $clone = $petSelect.clone();
                else if (val == 'Pet Variant') $clone = $petVariantSelect.clone();
                else if (val == 'Currency') $clone = $currencySelect.clone();
                @if ($showLootTables)
                    else if (val == 'LootTable') $clone = $tableSelect.clone();
                @endif
                @if ($showRaffles)
                    else if (val == 'Raffle') $clone = $raffleSelect.clone();
                @endif
                @if (isset($showThemes) && $showThemes)
                    else if (val == 'Theme') $clone = $themeSelect.clone();
                @endif
                @if (isset($showRecipes) && $showRecipes)
                    else if (val == 'Recipe') $clone = $recipeSelect.clone();
                @endif

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
