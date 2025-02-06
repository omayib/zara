// File: js/admin-sync.js
(function($) {
    // Define the function in a safe namespace or expose it to the global scope as needed.
    function performCustomAction(postId) {
        if (confirm('Do you want to perform this action?')) {
            var $btn = $('#sync-button-' + postId);
            $btn.attr('disabled', 'disabled');
            $btn.find('.spinner').addClass('is-active');
            $btn.find('.button-text').hide();

            $.ajax({
                url: customSync.ajaxurl, // Provided via wp_localize_script
                type: 'POST',
                data: {
                    action: 'custom_row_action',
                    post_id: postId,
                    security: customSync.nonce // Provided via wp_localize_script
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        // Optionally refresh the page or update the UI accordingly
                        location.reload();
                    } else {
                        alert('Action failed: ' + response.data);
                    }
                },
                error: function() {
                    alert('An error occurred');
                },
                complete: function() {
                    $btn.removeAttr('disabled');
                    $btn.find('.spinner').removeClass('is-active');
                    $btn.find('.button-text').show();
                }
            });
        }
    }

    // Expose the function globally if needed
    window.performCustomAction = performCustomAction;
})(jQuery);
