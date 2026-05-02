jQuery(document).ready(function($) {
    'use strict';

    function submitTemplate(postTitle, templateType, postType, displayRules, exclusionRules, userRoles, canvasTemplate, editPostId, saveOnly) {
        let formData = {
            action: 'primekit_library_new_post',
            postTitle: postTitle,
            templateType: templateType,
            postType: postType || 'primekit_library',
            security: primekitNewTemplateCreated.nonce,
        };
        if (displayRules   !== undefined) formData.displayRules   = displayRules;
        if (exclusionRules !== undefined) formData.exclusionRules = exclusionRules;
        if (userRoles      !== undefined) formData.userRoles      = userRoles;
        if (canvasTemplate !== undefined) formData.canvasTemplate = canvasTemplate;
        if (editPostId)                   formData.editPostId     = editPostId;
        if (saveOnly)                     formData.saveOnly       = '1';

        $.ajax({
            type: 'POST',
            url: primekitNewTemplateCreated.ajaxurl,
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert(response.data.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('AJAX error: ' + textStatus + ' - ' + errorThrown);
            }
        });
    }

    function collectAndSubmitHF(saveOnly) {
        let displayRules = [];
        $('#primekit-hf-display-rows .primekit-hf-rule-condition').each(function() {
            let v = $(this).val();
            if (v) displayRules.push(v);
        });

        let exclusionRules = [];
        if ($('#primekit-hf-exclusion-section').is(':visible')) {
            $('#primekit-hf-exclusion-rows .primekit-hf-rule-condition').each(function() {
                let v = $(this).val();
                if (v) exclusionRules.push(v);
            });
        }

        let userRoles = [];
        $('#primekit-hf-user-rows .primekit-hf-user-condition').each(function() {
            let v = $(this).val();
            if (v) userRoles.push(v);
        });

        submitTemplate(
            $('#primekit-hf-modal-ftemplate-name').val(),
            $('#primekit-hf-modal-select-type').val(),
            'primekit_library',
            JSON.stringify(displayRules),
            JSON.stringify(exclusionRules),
            JSON.stringify(userRoles),
            $('#primekit-hf-canvas-template').is(':checked') ? '1' : '0',
            $('#primekit-hf-edit-post-id').val() || null,
            saveOnly
        );
    }

    // Theme Builder modal — enable submit when type + name filled
    $('#primekit-tb-modal-ftemplate-name, #primekit-tb-modal-select-template-type').on('change keyup', function() {
        let name = $('#primekit-tb-modal-ftemplate-name').val().trim();
        let type = $('#primekit-tb-modal-select-template-type').val();
        $('#primekit-tb-modal-content-form-submit').prop('disabled', !(name && type));
    });

    // Theme Builder modal — submit
    $('#primekit-tb-modal-template-form').on('submit', function(e) {
        e.preventDefault();
        submitTemplate(
            $('#primekit-tb-modal-ftemplate-name').val(),
            $('#primekit-tb-modal-select-template-type').val(),
            $('#primekit-tb-post-type').val()
        );
    });

    // Header Footer modal — Save button (saves settings, stays on list)
    $('#primekit-hf-modal-save-btn').on('click', function() {
        collectAndSubmitHF(true);
    });

    // Header Footer modal — submit button (saves + opens Elementor)
    $('#primekit-hf-modal-template-form').on('submit', function(e) {
        e.preventDefault();
        collectAndSubmitHF(false);
    });
});
