<script>
    $(document).ready(function() {
        $('.accept-link').on('click', function(e) {
            e.preventDefault();
            let $row = $(this).parent().parent().parent();
            let id = $(this).data('link-id');
            let notificationId = $row.find('.clear-notification').data('id');

            console.log(notificationId);

            $.ajax({
                url: '{{ url('links/accept') }}/' + id,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                method: 'POST',
                success: function(data) {
                    $.ajax({
                        url: "{{ url('notifications/delete') }}/" + notificationId,
                        method: 'GET',
                        success: function(data) {
                            location.reload();
                        }
                    });
                },
                error: function(data) {
                    location.reload();
                }
            });
        });

        $('.delete-link').on('click', function(e) {
            e.preventDefault();
            let $row = $(this).parent().parent().parent();
            let id = $(this).data('link-id');
            let notificationId = $row.find('.clear-notification').data('id');

            $.ajax({
                url: '{{ url('links/delete') }}/' + id,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                method: 'POST',
                success: function(data) {
                    $.ajax({
                        url: "{{ url('notifications/delete') }}/" + notificationId,
                        method: 'GET',
                        success: function(data) {
                            location.reload();
                        }
                    });
                },
                error: function(data) {
                    location.reload();
                }
            });
        });
    });
</script>
