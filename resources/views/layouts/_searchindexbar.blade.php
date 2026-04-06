<li class="search-bar">
    <span class="badge badge-dark"></span>
    <input class="dark-input" id="ajaxsearch" type="text" data-type="" placeholder="Search site..." />
    <div class="dropdown" id="searchResult" style="display:none;">
        <div id="listResults"></div>
    </div>
<li>

    <script>
        $(document).ready(function() {
            let timer;
            var $badge = $('.search-bar > .badge');
            var dataType;

            $('#ajaxsearch').keyup(function() {
                clearTimeout(timer);
                let s = $(this).val();

                if ( s.includes(':') ) {
                    dataType = s.split(':')[0];
                    $badge.text(dataType.toUpperCase());
                    $(this).val('');
                    $(this).attr('data-type', dataType);
                }

                timer = setTimeout(() => {
                    getSearchResults(s, dataType);
                }, 300);
            });

            $('#ajaxsearch').on('keydown', function(e) {
                if ( $(this).val() == '' && (e.key === 'Backspace' || e.key === 'Escape') ) {
                    $badge.text('');
                    $('#ajaxsearch').attr('data-type', '');
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

            $badge.click(function() {
                $badge.text('');
                $('#ajaxsearch').attr('data-type', '');
            });

            function getSearchResults(s, dataType = null) {
                if (s != '' || s != null) {
                    $.ajax({
                        url: "/asearch",
                        method: "GET",
                        data: {
                            s: s,
                            type: dataType
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
            }
        });
    </script>
