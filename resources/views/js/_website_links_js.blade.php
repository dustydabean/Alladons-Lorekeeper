<script>
    $(document).ready(function() {
        var $addLink = $('#addLink');
        var $components = $('#linkComponents');
        var $linkRow = $('#linkRow').find('.link-row');
        var $linkTable = $('#linkTable');
        var count = 0;

        attachRemoveListener($('.remove-link-button'));

        $addLink.on('click', function(e) {
            e.preventDefault();
            $clone = $linkRow.clone();
            $linkTable.append($clone);
            attachRemoveListener($clone.find('.remove-link-button'));
            count++;
        });

        function attachRemoveListener(node) {
            node.on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        }

        $('.handle').on('click', function(e) {
            e.preventDefault();
        });
        $linkTable.sortable({
            prompts: '.link-row',
            handle: ".handle"
        });
    });
</script>