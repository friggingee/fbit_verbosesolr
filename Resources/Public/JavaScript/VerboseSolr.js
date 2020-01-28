define(['jquery'], function ($) {
    var VerboseSolr = {

    };

    VerboseSolr.init = function () {
        if ($('.indexqueueitems').length > 0) {
            $('.indexqueueitems a.reindex').on('click', function() {
                $.ajax({
                    url: TYPO3.settings.ajaxUrls['verboseSolr_reindexItem'],
                    method: 'GET',
                    data: {
                        item_uid: $(this).data('itemid')
                    },
                    error: function (xhr, status, error) {
                        // reload the module to show the changes.
                        frameElement.src = frameElement.src;
                    },
                    success: function(data, status, xhr) {
                        // reload the module to show the changes.
                        frameElement.src = frameElement.src;
                    }
                });
            });

            $('a[data-error-id]').on('click', function(event) {
                var $errorText = $('div[data-error-id="' + $(event.target).data('errorId') + '"]');

                if ($errorText.is(':visible')) {
                    $errorText.hide();
                } else {
                    $errorText.show();
                }
            });
        }
    };

    $(document).ready(VerboseSolr.init());

    // To let the module be a dependency of another module, we return our object
    return VerboseSolr;
});
