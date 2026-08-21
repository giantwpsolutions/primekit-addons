(function ($) {
    'use strict';

    var Wizard = {

        init: function () {
            this.bindFeatureForm();
        },

        /**
         * Handle the Features step form submission via AJAX.
         */
        bindFeatureForm: function () {
            var $form = $('#pkwiz-feature-form');
            if (!$form.length) return;

            $form.on('submit', function (e) {
                e.preventDefault();

                var $btn = $('#pkwiz-save-features');
                $btn.addClass('is-loading').prop('disabled', true);

                var features = [];
                $form.find('input[name="features[]"]:checked').each(function () {
                    features.push($(this).val());
                });

                $.post(
                    primekitWizard.ajaxUrl,
                    {
                        action: 'primekit_wizard_save_features',
                        nonce:   primekitWizard.nonce,
                        features: features,
                    },
                    function (response) {
                        if (response.success && response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            $btn.removeClass('is-loading').prop('disabled', false);
                            alert(response.data && response.data.message
                                ? response.data.message
                                : 'Something went wrong. Please try again.');
                        }
                    }
                ).fail(function () {
                    $btn.removeClass('is-loading').prop('disabled', false);
                    alert('Network error. Please try again.');
                });
            });
        },
    };

    $(document).ready(function () {
        Wizard.init();
    });

})(jQuery);
