(function($) {
    'use strict';

    var PrimeKitTemplateLibrary = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '.primekit-template-insert', this.insertTemplate.bind(this));
        },

        insertTemplate: function(e) {
            e.preventDefault();
            var $this = $(e.currentTarget);
            var templateId = $this.data('template-id');

            if (!templateId) {
                alert('Template ID not found');
                return;
            }

            $this.addClass('loading');

            elementor.templates.requestTemplateContent(
                'primekit-library',
                {
                    source: 'remote',
                    template_id: templateId,
                    edit_mode: true
                },
                {
                    success: function(data) {
                        if (data && data.content) {
                            $e.run('document/elements/import', {
                                model: elementor.elementsModel,
                                data: {
                                    content: data.content
                                }
                            });
                            elementor.templates.closeModal();
                        } else {
                            alert('Failed to insert the template. Invalid template data.');
                        }
                    },
                    error: function() {
                        alert('Failed to insert the template. Please try again.');
                    },
                    complete: function() {
                        $this.removeClass('loading');
                    }
                }
            );
        }
    };

    $(window).on('elementor:init', function() {
        PrimeKitTemplateLibrary.init();
    });

})(jQuery);