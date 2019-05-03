(function ($) {

    var el_form = $('#testimonial-form-new-post'),
        el_form_submit = $('.submit', el_form);

    // Fires when the form is submitted.
    el_form.on('submit', function (e) {
        e.preventDefault();

        new_post();
    });

    // Ajax request.
    function new_post() {
        $.ajax({
            url: localized_testimonial_post_form.admin_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'testimonial_new_post', // Set action without prefix 'wp_ajax_'.
                form_data: el_form.serialize()
            },
            cache: false
        }).done(function (r) {
            $("#post_title").val('');
            $("#post_content").val('');
            alert("Testimonial created!! (This ugly alert box will be replaced)");
            location.reload();
        });
    }

})(jQuery);