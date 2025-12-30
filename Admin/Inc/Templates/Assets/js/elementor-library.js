/**
 * PrimeKit Elementor Library Integration
 *
 * Adds a custom "PrimeKit Templates" tab to Elementor's template library modal.
 * Handles fetching, displaying, and inserting remote templates.
 *
 * @package PrimeKit
 * @since 1.2.11
 */

(function ($) {
    'use strict';

    /**
     * PrimeKit Library Manager
     */
    var PrimeKitLibraryManager = {

        libraryData: null,
        isLoading: false,
        templates: [],

        /**
         * Initialize
         */
        init: function () {
            var self = this;

            // Wait for Elementor to be ready
            $(window).on('elementor:init', function () {
                // Register our template source
                elementor.on('preview:loaded', function () {
                    self.registerSource();
                });
            });
        },

        /**
         * Register template source with Elementor
         */
        registerSource: function () {
            var self = this;

            // Extend Elementor's remote template library source
            var PrimeKitSource = elementor.modules.templateLibrary.classes.BaseTemplateSource.extend({

                getType: function () {
                    return 'primekit-library';
                },

                getTypeTitle: function () {
                    return 'PrimeKit Templates';
                },

                getCategories: function () {
                    return {
                        pages: {
                            title: 'Pages',
                            defaultActive: true
                        }
                    };
                },

                getTags: function () {
                    if (self.libraryData && self.libraryData.tags) {
                        return self.libraryData.tags;
                    }
                    return [];
                },

                getItems: function (args) {
                    var deferred = jQuery.Deferred();

                    if (self.templates.length > 0) {
                        deferred.resolve(self.templates);
                    } else {
                        self.loadTemplates().then(function (templates) {
                            deferred.resolve(templates);
                        }).fail(function () {
                            deferred.reject();
                        });
                    }

                    return deferred.promise();
                },

                getItem: function (templateId) {
                    var deferred = jQuery.Deferred();

                    self.loadTemplateContent(templateId).then(function (data) {
                        deferred.resolve(data);
                    }).fail(function () {
                        deferred.reject();
                    });

                    return deferred.promise();
                }
            });

            // Register the source
            elementor.templates.registerTemplateSource(new PrimeKitSource());

            // Bind events after registration
            this.bindEvents();
        },

        /**
         * Load templates from remote API
         */
        loadTemplates: function () {
            var self = this;
            var deferred = jQuery.Deferred();

            if (self.isLoading) {
                return deferred.promise();
            }

            self.isLoading = true;

            elementorCommon.ajax.addRequest('get_primekit_library_data', {
                data: {},
                success: function (response) {
                    self.libraryData = response;
                    self.templates = response.templates || [];

                    // Format templates for Elementor
                    var formattedTemplates = [];
                    $.each(self.templates, function (index, template) {
                        formattedTemplates.push({
                            template_id: template.template_id,
                            title: template.title,
                            type: template.type || 'page',
                            thumbnail: template.thumbnail,
                            date: template.date,
                            tags: template.tags || [],
                            isPro: template.isPro || false,
                            url: template.url,
                            subtype: 'page',
                            source: 'primekit-library'
                        });
                    });

                    self.templates = formattedTemplates;
                    self.isLoading = false;
                    deferred.resolve(formattedTemplates);
                },
                error: function (error) {
                    self.isLoading = false;
                    deferred.reject(error);
                }
            });

            return deferred.promise();
        },

        /**
         * Load template content for insertion
         */
        loadTemplateContent: function (templateId) {
            var deferred = jQuery.Deferred();

            elementorCommon.ajax.addRequest('get_primekit_template_data', {
                data: {
                    template_id: templateId,
                    editor_post_id: elementor.config.document.id
                },
                success: function (data) {
                    if (data && data.content) {
                        deferred.resolve(data);
                    } else {
                        deferred.reject('Invalid template data');
                    }
                },
                error: function (error) {
                    deferred.reject(error);
                }
            });

            return deferred.promise();
        },

        /**
         * Bind UI events
         */
        bindEvents: function () {
            var self = this;

            // Handle template insertion from modal
            elementor.on('template:before:insert', function (templateModel) {
                if (templateModel.get('source') === 'primekit-library') {
                    // Template is being inserted
                    console.log('PrimeKit template inserting:', templateModel.get('title'));
                }
            });

            // Handle template insert success
            elementor.on('template:after:insert', function (templateModel) {
                if (templateModel.get('source') === 'primekit-library') {
                    self.showNotice('success', 'Template inserted successfully!');
                }
            });
        },

        /**
         * Show notification
         */
        showNotice: function (type, message) {
            if (typeof elementorCommon !== 'undefined' && elementorCommon.dialogsManager) {
                elementorCommon.dialogsManager.createWidget('alert', {
                    id: 'primekit-library-notice-' + Date.now(),
                    message: message,
                    position: {
                        my: 'center center',
                        at: 'center center'
                    },
                    onConfirm: function () {
                        this.hide();
                    }
                }).show();
            }
        }
    };

    /**
     * Initialize when ready
     */
    $(function () {
        PrimeKitLibraryManager.init();
    });

})(jQuery);
