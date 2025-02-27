<script>
    $(document).on('click', '.form-data-button', function() {
        // get value from data-id="" attribute
        var id = $(this).attr("data-id");
        // ajax get
        $.ajax({
            type: "GET",
            url: "{{ url('character/' . $character->slug . '/image') }}/" + id,
            dataType: "text"
        }).done(function(res) {
            $("#main-tab").fadeOut(500, function() {
                $("#main-tab").html(res);
                $("#main-tab").fadeIn(500);
            });
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert("AJAX call failed: " + textStatus + ", " + errorThrown);
        });
    });
</script>
