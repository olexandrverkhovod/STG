jQuery(function ($) {
    $('body').on('click', '.view-post', function () {
        var data = {
            'action': 'load_post_by_ajax',
            'id': $(this).data('id'),
            'security': staff.security
        };

        $.post(staff.ajaxurl, data, function (response) {
            response = JSON.parse(response);
            let imageUrl = response.photo;
            $('#postModal .modal-image').css('background-image', 'url(' + imageUrl + ')');
            $('#postModal h2#postModalTitle').text(response.title);
            $('#postModal h3#postModalSubtitle').text(response.subfield);
            $('#postModal .modal-right-side p').text(response.content);

            $('#postModal').modal({ show: true });
        });
    });
});