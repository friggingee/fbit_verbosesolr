define(['jquery'], function ($) {
    var VerboseSolr = {

    };

    VerboseSolr.init = function () {
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
    };

    $(document).ready(VerboseSolr.init());

    // To let the module be a dependency of another module, we return our object
    return VerboseSolr;
});
