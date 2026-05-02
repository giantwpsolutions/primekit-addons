jQuery(document).ready(function($) {
    var searchParams = new URLSearchParams(window.location.search);
    var isHFPage     = searchParams.get('primekit_filter') === 'hf';

    /* ── Sidebar active state ── */
    if (window.location.pathname.includes('edit.php') && searchParams.get('post_type') === 'primekit_library' ||
        window.location.pathname.includes('post-new.php')) {
        $('#toplevel_page_primekit_home')
            .addClass('wp-has-current-submenu wp-menu-open')
            .removeClass('wp-not-current-submenu');
        $('#toplevel_page_primekit_home .wp-submenu li').removeClass('current');
        var activeHref = isHFPage
            ? 'edit.php?post_type=primekit_library&primekit_filter=hf'
            : 'edit.php?post_type=primekit_library';
        $('#toplevel_page_primekit_home .wp-submenu li a[href="' + activeHref + '"]').parent().addClass('current');
    }

    /* ── Default Elementor canvas template ── */
    var $tpl = $('#page_template');
    if ($tpl.length) $tpl.val('elementor_canvas').trigger('change');

    /* ── TB modal: enable submit when name + type filled ── */
    $('#primekit-tb-modal-ftemplate-name, #primekit-tb-modal-select-template-type').on('change keyup', function() {
        var name = $('#primekit-tb-modal-ftemplate-name').val().trim();
        var type = $('#primekit-tb-modal-select-template-type').val();
        $('#primekit-tb-modal-content-form-submit').prop('disabled', !(name && type));
    });

    /* ── HF modal: enable submit when name + type filled ── */
    function updateHFSubmit() {
        var name    = $('#primekit-hf-modal-ftemplate-name').val().trim();
        var type    = $('#primekit-hf-modal-select-type').val();
        var enabled = !!(name && type);
        $('#primekit-hf-modal-content-form-submit').prop('disabled', !enabled);
        $('#primekit-hf-modal-save-btn').prop('disabled', !enabled);
    }
    $('#primekit-hf-modal-ftemplate-name, #primekit-hf-modal-select-type').on('change keyup', updateHFSubmit);

    /* ═══════════════════════════════════════════
       Repeater row helpers
       ═══════════════════════════════════════════ */

    function updateDeleteVisibility($rowsContainer, alwaysShow) {
        var $rows = $rowsContainer.find('.primekit-hf-rule-row');
        $rows.each(function() {
            var $btn = $(this).find('.primekit-hf-rule-delete');
            if (alwaysShow || $rows.length > 1) {
                $btn.removeClass('primekit-hf-hidden');
            } else {
                $btn.addClass('primekit-hf-hidden');
            }
        });
    }

    /* Add Display Rule */
    $('#primekit-hf-add-display-rule').on('click', function(e) {
        e.preventDefault();
        var $this  = $(this);
        var newId  = parseInt($this.attr('data-rule-id')) + 1;
        var tpl    = wp.template('primekit-hf-display-condition');
        $('#primekit-hf-display-rows').append(tpl({ id: newId }));
        $this.attr('data-rule-id', newId);
        updateDeleteVisibility($('#primekit-hf-display-rows'), false);
    });

    /* Show Exclusion section */
    $('#primekit-hf-show-exclusion').on('click', function(e) {
        e.preventDefault();
        $(this).hide();
        $('#primekit-hf-exclusion-section').slideDown(250);
        updateDeleteVisibility($('#primekit-hf-exclusion-rows'), true);
    });

    /* Add Exclusion Rule */
    $('#primekit-hf-add-exclusion-rule').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var newId = parseInt($this.attr('data-rule-id')) + 1;
        var tpl   = wp.template('primekit-hf-display-condition');
        $('#primekit-hf-exclusion-rows').append(tpl({ id: newId }));
        $this.attr('data-rule-id', newId);
        updateDeleteVisibility($('#primekit-hf-exclusion-rows'), true);
    });

    /* Add User Role */
    $('#primekit-hf-add-user-rule').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var newId = parseInt($this.attr('data-rule-id')) + 1;
        var tpl   = wp.template('primekit-hf-user-condition');
        $('#primekit-hf-user-rows').append(tpl({ id: newId }));
        $this.attr('data-rule-id', newId);
        updateDeleteVisibility($('#primekit-hf-user-rows'), false);
    });

    /* Delete any rule row */
    $(document).on('click', '.primekit-hf-rule-delete', function() {
        var $row      = $(this).closest('.primekit-hf-rule-row');
        var $rowsWrap = $row.closest('.primekit-hf-rule-rows');
        var isExcl    = $rowsWrap.attr('id') === 'primekit-hf-exclusion-rows';

        if (isExcl && $rowsWrap.find('.primekit-hf-rule-row').length <= 1) {
            $rowsWrap.find('.primekit-hf-rule-condition').val('');
            $('#primekit-hf-exclusion-section').slideUp(200);
            $('#primekit-hf-show-exclusion').show();
            return;
        }

        $row.remove();

        var isUser = $rowsWrap.attr('id') === 'primekit-hf-user-rows';
        updateDeleteVisibility($rowsWrap, isExcl);

        var lastIdx = $rowsWrap.find('.primekit-hf-rule-row').last().data('index') || 0;
        if (isExcl) {
            $('#primekit-hf-add-exclusion-rule').attr('data-rule-id', lastIdx);
        } else if (isUser) {
            $('#primekit-hf-add-user-rule').attr('data-rule-id', lastIdx);
        } else {
            $('#primekit-hf-add-display-rule').attr('data-rule-id', lastIdx);
        }
    });

    /* ═══════════════════════════════════════════
       Modal reset / pre-fill
       ═══════════════════════════════════════════ */

    function resetHFModal() {
        $('#primekit-hf-edit-post-id').val('');
        $('#primekit-hf-modal-title').text(
            (typeof primekitHFTemplates !== 'undefined') ? primekitHFTemplates._strings.addNew : 'Add New Template'
        );
        $('#primekit-hf-modal-ftemplate-name').val('');
        $('#primekit-hf-modal-select-type').val('');
        $('#primekit-hf-canvas-template').prop('checked', false);

        var $disp = $('#primekit-hf-display-rows');
        $disp.empty().append($(wp.template('primekit-hf-display-condition')({ id: 0 })));
        $('#primekit-hf-add-display-rule').attr('data-rule-id', 0);
        updateDeleteVisibility($disp, false);

        var $excl = $('#primekit-hf-exclusion-rows');
        $excl.empty().append($(wp.template('primekit-hf-display-condition')({ id: 0 })));
        $('#primekit-hf-add-exclusion-rule').attr('data-rule-id', 0);
        $('#primekit-hf-exclusion-section').hide();
        $('#primekit-hf-show-exclusion').show();

        var $user = $('#primekit-hf-user-rows');
        $user.empty().append($(wp.template('primekit-hf-user-condition')({ id: 0 })));
        $('#primekit-hf-add-user-rule').attr('data-rule-id', 0);
        updateDeleteVisibility($user, false);

        $('#primekit-hf-modal-content-form-submit').prop('disabled', true);
        $('#primekit-hf-modal-save-btn').prop('disabled', true);
    }

    function populateHFModal(data) {
        $('#primekit-hf-edit-post-id').val(data.id);
        $('#primekit-hf-modal-title').text(
            (typeof primekitHFTemplates !== 'undefined') ? primekitHFTemplates._strings.editTemplate : 'Edit Template'
        );
        $('#primekit-hf-modal-ftemplate-name').val(data.title);
        $('#primekit-hf-modal-select-type').val(data.type);
        $('#primekit-hf-canvas-template').prop('checked', data.canvas);

        /* Display rows */
        var $disp    = $('#primekit-hf-display-rows');
        var dispRules = data.displayRules.length ? data.displayRules : [''];
        $disp.empty();
        $.each(dispRules, function(i, rule) {
            var $row = $(wp.template('primekit-hf-display-condition')({ id: i }));
            if (rule) $row.find('.primekit-hf-rule-condition').val(rule);
            $disp.append($row);
        });
        $('#primekit-hf-add-display-rule').attr('data-rule-id', dispRules.length - 1);
        updateDeleteVisibility($disp, false);

        /* Exclusion rows */
        var $excl = $('#primekit-hf-exclusion-rows');
        $excl.empty();
        if (data.exclusionRules.length) {
            $.each(data.exclusionRules, function(i, rule) {
                var $row = $(wp.template('primekit-hf-display-condition')({ id: i }));
                if (rule) $row.find('.primekit-hf-rule-condition').val(rule);
                $excl.append($row);
            });
            $('#primekit-hf-add-exclusion-rule').attr('data-rule-id', data.exclusionRules.length - 1);
            $('#primekit-hf-exclusion-section').show();
            $('#primekit-hf-show-exclusion').hide();
            updateDeleteVisibility($excl, true);
        } else {
            $excl.append($(wp.template('primekit-hf-display-condition')({ id: 0 })));
            $('#primekit-hf-add-exclusion-rule').attr('data-rule-id', 0);
            $('#primekit-hf-exclusion-section').hide();
            $('#primekit-hf-show-exclusion').show();
        }

        /* User role rows */
        var $user     = $('#primekit-hf-user-rows');
        var userRoles = data.userRoles.length ? data.userRoles : [''];
        $user.empty();
        $.each(userRoles, function(i, role) {
            var $row = $(wp.template('primekit-hf-user-condition')({ id: i }));
            if (role) $row.find('.primekit-hf-user-condition').val(role);
            $user.append($row);
        });
        $('#primekit-hf-add-user-rule').attr('data-rule-id', userRoles.length - 1);
        updateDeleteVisibility($user, false);

        updateHFSubmit();
    }

    /* ── Open correct modal on "Add New" ── */
    $('a.page-title-action').on('click', function(e) {
        e.preventDefault();
        if (isHFPage) {
            resetHFModal();
            MicroModal.show('primekit-hf-modal');
        } else {
            MicroModal.show('primekit-tb-modal');
        }
    });

    /* ── Open HF modal pre-filled on "Edit" for H/F templates ── */
    $(document).on('click', '.primekit-hf-edit-link', function(e) {
        e.preventDefault();
        var postId = $(this).data('post-id');
        var data   = (typeof primekitHFTemplates !== 'undefined') ? primekitHFTemplates[postId] : null;
        if (!data) return;
        populateHFModal(data);
        MicroModal.show('primekit-hf-modal');
    });
});
