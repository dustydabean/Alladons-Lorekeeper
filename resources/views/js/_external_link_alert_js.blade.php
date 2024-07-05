<script>
    $(function() {
        let allowedUrls = [
            "https://github.com/corowne/lorekeeper", // for credits
            "mailto:{{ env('CONTACT_ADDRESS') }}", // for contact
            "http://deviantart.com/{{ env('DEVIANTART_ACCOUNT') }}", // for credits
        ]
        $('a').each(function() {
            let link = $(this);
            let isExternal = this.host !== window.location.host && !allowedUrls.includes(this.href);

            if (isExternal) {
                link.addClass('external-link');
            }
        });

        $('.external-link').on('click', function(event) {
            event.preventDefault();
            let url = $(this).attr('href');
            let externalHtml = `
                <p>
                    You are about to leave this site. Are you sure you want to continue?
                </p>
                <div class="alert alert-danger">
                    ${url}
                </div>
                <a class="btn btn-block btn-success mb-3" href="${url}" target="_blank">
                    Continue
                </a>
                <p>Don't recognise this link? Click below to cancel and return to the previous page.</p>
                <button class="btn btn-block btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
            `;
            $('#modal').find('.modal-body').html('');
            $('#modal').find('.modal-title').html('External Link');
            $('#modal').find('.modal-body').html(externalHtml);
            $('#modal').modal('show');
        });
    });
</script>