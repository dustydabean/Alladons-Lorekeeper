<script>
    $(document).ready(function() {
        var maxCompanions = 10;
        var showExp = {{ isset($showExp) && $showExp ? 'true' : 'false' }};

        function updateCount() {
            var count = $('.companion-checkbox:checked').length;
            $('#companionCount').text(count + '/' + maxCompanions + ' selected');
            if (count >= maxCompanions) {
                $('#companionCount').removeClass('text-muted').addClass('text-danger');
            } else {
                $('#companionCount').removeClass('text-danger').addClass('text-muted');
            }
        }

        function applyFilters() {
            var category = $('#companionCategoryFilter').val();
            var search = $('#companionSearch').val().toLowerCase().trim();

            $('.companion-entry').each(function() {
                var matchCategory = (category === 'all' || $(this).data('category') == category);
                var matchSearch = (!search || $(this).data('search').indexOf(search) !== -1);
                $(this).toggleClass('d-none', !(matchCategory && matchSearch));
            });
        }

        $('#companionGrid').on('click', '.companion-box', function(e) {
            if (showExp && ($(e.target).hasClass('companion-exp-input') || $(e.target).closest('.companion-exp-field').length)) {
                return;
            }
            e.preventDefault();
            var $box = $(this);
            var $checkbox = $box.find('.companion-checkbox');
            var isChecked = $checkbox.prop('checked');

            if (!isChecked && $('.companion-checkbox:checked').length >= maxCompanions) {
                return;
            }

            $checkbox.prop('checked', !isChecked);
            $box.toggleClass('border-primary companion-selected', !isChecked);

            if (showExp) {
                var $expField = $box.find('.companion-exp-field');
                if (!isChecked) {
                    $expField.removeClass('d-none');
                } else {
                    $expField.addClass('d-none');
                    $expField.find('.companion-exp-input').val(0);
                }
            }

            updateCount();
        });

        $('#companionCategoryFilter').on('change', applyFilters);
        $('#companionSearch').on('input', applyFilters);

        updateCount();
    });
</script>
<style>
    .companion-box {
        transition: border-color 0.15s, background-color 0.15s;
    }
    .companion-box:hover {
        background-color: rgba(0,123,255,0.05);
    }
    .companion-selected {
        border-width: 2px !important;
        background-color: rgba(0,123,255,0.1);
    }
</style>
