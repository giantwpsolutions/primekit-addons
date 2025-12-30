(function ($, elementor) {
    "use strict";

    const primekitLibrary = {
        init() {
            this.bindEvents();
        },

        bindEvents() {
            elementor.on("preview:loaded", this.onElementorPreviewLoaded.bind(this));
            $(document).on("click", ".primekit-template-insert", this.onTemplateInsertClick.bind(this));
        },

        onElementorPreviewLoaded() {
            const $previewContents = window.elementor.$previewContents;

            const interval = setInterval(() => {
                const targetSelector = ".elementor-add-new-section .elementor-add-section-drag-title";
                const target = $previewContents.find(targetSelector);

                if (target.length) {
                    clearInterval(interval);
                    this.addLibraryButton($previewContents);
                }
            }, 100);
        },

        addLibraryButton($previewContents) {
            const iconUrl = primekitTemplates.pluginUrl + 'Admin/Assets/img/icon-white.png';
            const customButtonHTML = `
            <div class="elementor-add-section-area-button primekit-library-open-button primekit-add-button" id="insertStaticTemplateButton" style="background: linear-gradient(135deg, #0049E7 0%, #C835F8 100%) !important; width: 40px !important; height: 40px !important; border-radius: 50% !important; display: inline-flex !important; align-items: center !important; justify-content: center !important;">
                <img src="${iconUrl}" alt="PrimeKit Templates" style="width: 22px !important; height: 22px !important; filter: brightness(0) invert(1) !important;" />
            </div>
        `;

            if (!$previewContents.find(".primekit-library-open-button").length) {
                const addSection = $previewContents.find(".elementor-add-section-inner");
                if (addSection.length) {
                    const existingButtons = addSection.find(".elementor-add-section-area-button");

                    if (existingButtons.length >= 2) {
                        $(existingButtons[1]).after(customButtonHTML);
                    } else {
                        addSection.prepend(customButtonHTML);
                    }

                    $previewContents.on("click", ".primekit-library-open-button", () => {
                        primekitNamespace.showModal();
                    });
                }
            }
        },

        onTemplateInsertClick(event) {
            event.preventDefault();
            const $button = $(event.currentTarget);
            const templateId = $button.data("template-id");

            primekitNamespace.insertTemplate(templateId);
        },
    };

    $(document).ready(() => {
        primekitLibrary.init();
    });
})(jQuery, window.elementor);