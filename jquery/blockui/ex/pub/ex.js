(function (jQuery) {
    jQuery.blockUIEx = function (options) {
        if (options.exCenter) {
            var height = jQuery(window).height();
            var width = jQuery(document).width();

            if (!options.css) {
                options.css = {
                    padding: 0,
                    margin: 0,
                    background: 'transparent',
                    border: 'none',
                    textAlign: 'none',
                    width: options.message.width(),
                    height: options.message.height()
                };
            }

            options.css['left'] = width / 2 - (options.message.width() / 2);
            options.css['top'] = height / 2 - (options.message.height() / 2);
        }

        jQuery.blockUI(options);
    }

    jQuery.unblockUIFast = function () {
        jQuery.unblockUI({ fadeOut: 0 });
    }
})(jQuery);