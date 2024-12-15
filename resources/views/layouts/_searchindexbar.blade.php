<li class="search-bar">
    <input class="dark-input" id="ajaxsearch" type="text" placeholder="Search site..." />
    <div class="dropdown" id="searchResult" style="display:none;">
        <div id="listResults"></div>
    </div>
<li>

<script>
        $(document).ready(function() {
            $('#ajaxsearch').keyup(function() {
                let s = $(this).val();
                if (s != '' || s != null) {
                    $.ajax({
                        //url: "/ajax-s-process.php",
                        url: "/asearch",
                        method: "GET",
                        data: {
                            s: s
                        },
                        beforeSend: function() {
                            $(".search-bar").addClass('loader');
                            $(".dark-input").addClass('active');
                        },
                        complete: function() {
                            $(".search-bar").removeClass('loader');
                        },
                        success: function(data) {
                            $("#searchResult").html(data);
                            $("#searchResult").show();
                        }
                    })
                } else {
                    $("searchResult").hide();
                }
            });
            $(document).on("click", function(event) {
                if (!$(event.target).closest("#searchResult").length && $("#searchResult").is(':visible')) {
                    $('#searchResult').fadeOut();
                    $("searchResult").hide();
                    $('#ajaxsearch').val('');
                    $(".dark-input").removeClass('active');
                } else {
                    $("searchResult").show();
                }
            });
        });
    </script>
    