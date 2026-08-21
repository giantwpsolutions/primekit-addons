<?php
/**
 * Setup Wizard — Full-page HTML template.
 *
 * Variables available from SetupWizard::render_wizard():
 *   $step     (string)  — current step slug
 *   $features (array)   — saved feature option values
 *   $this     (SetupWizard) — wizard instance
 */
if (!defined('ABSPATH')) exit;

$steps       = $this->get_steps();
$step_slugs  = array_keys($steps);
$step_index  = array_search($step, $step_slugs, true);

$icon_url    = PRIMEKIT_URL . 'Admin/Assets/img/icon-white.png';
$logo_url    = PRIMEKIT_URL . 'Admin/Assets/img/Icon.png';

// Gather enqueued styles/scripts so they render in our custom page
ob_start();
do_action('admin_print_styles');
do_action('admin_print_scripts');
$wp_head_output = ob_get_clean();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('PrimeKit Setup Wizard', 'primekit-addons'); ?></title>
    <?php echo $wp_head_output; // phpcs:ignore WordPress.Security.EscapeOutput -- trusted WP output ?>
</head>
<body class="primekit-wizard-page">

<div class="pkwiz-wrap">

    <!-- ======= SIDEBAR ======= -->
    <aside class="pkwiz-sidebar">
        <div class="pkwiz-sidebar__logo">
            <img src="<?php echo esc_url($icon_url); ?>" alt="PrimeKit" width="36" height="36">
            <span><?php esc_html_e('PrimeKit', 'primekit-addons'); ?></span>
        </div>

        <nav class="pkwiz-steps" aria-label="<?php esc_attr_e('Setup Steps', 'primekit-addons'); ?>">
            <?php foreach ($steps as $slug => $label):
                $idx        = array_search($slug, $step_slugs, true);
                $is_current = ($slug === $step);
                $is_done    = ($idx < $step_index);
                $classes    = 'pkwiz-step';
                if ($is_current) $classes .= ' is-current';
                if ($is_done)    $classes .= ' is-done';
            ?>
            <div class="<?php echo esc_attr($classes); ?>">
                <div class="pkwiz-step__dot">
                    <?php if ($is_done): ?>
                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 10l4 4 6-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php else: ?>
                        <span><?php echo esc_html($idx + 1); ?></span>
                    <?php endif; ?>
                </div>
                <span class="pkwiz-step__label"><?php echo esc_html($label); ?></span>
            </div>
            <?php if ($idx < count($steps) - 1): ?>
                <div class="pkwiz-step__connector" aria-hidden="true"></div>
            <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="pkwiz-sidebar__footer">
            <span><?php echo esc_html('v' . PRIMEKIT_VERSION); ?></span>
            <a href="<?php echo esc_url(admin_url('admin.php?page=primekit_home')); ?>">
                <?php esc_html_e('Skip Setup', 'primekit-addons'); ?>
            </a>
        </div>
    </aside>

    <!-- ======= MAIN CONTENT ======= -->
    <main class="pkwiz-main" role="main">

        <?php if ($step === 'welcome'): ?>
        <!-- ─── STEP 1: WELCOME ─────────────────────────────── -->
        <div class="pkwiz-content pkwiz-content--welcome">
            <div class="pkwiz-welcome-hero">
                <img src="<?php echo esc_url($logo_url); ?>" alt="PrimeKit" class="pkwiz-hero-logo">
                <h1 class="pkwiz-heading">
                    <?php esc_html_e('Welcome to PrimeKit!', 'primekit-addons'); ?>
                </h1>
                <p class="pkwiz-subheading">
                    <?php esc_html_e('The most powerful Elementor addon for WordPress. Let\'s set everything up in just 2 quick steps.', 'primekit-addons'); ?>
                </p>
            </div>

            <div class="pkwiz-features-grid">
                <div class="pkwiz-feature-card">
                    <div class="pkwiz-feature-card__icon pkwiz-icon--widgets">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    </div>
                    <h3><?php esc_html_e('70+ Widgets', 'primekit-addons'); ?></h3>
                    <p><?php esc_html_e('Powerful Elementor widgets to build any type of page.', 'primekit-addons'); ?></p>
                </div>
                <div class="pkwiz-feature-card">
                    <div class="pkwiz-feature-card__icon pkwiz-icon--theme">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h3><?php esc_html_e('Theme Builder', 'primekit-addons'); ?></h3>
                    <p><?php esc_html_e('Design custom headers, footers, and archive pages visually.', 'primekit-addons'); ?></p>
                </div>
                <div class="pkwiz-feature-card">
                    <div class="pkwiz-feature-card__icon pkwiz-icon--templates">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                    </div>
                    <h3><?php esc_html_e('Template Library', 'primekit-addons'); ?></h3>
                    <p><?php esc_html_e('Import ready-made templates directly inside Elementor.', 'primekit-addons'); ?></p>
                </div>
                <div class="pkwiz-feature-card">
                    <div class="pkwiz-feature-card__icon pkwiz-icon--popup">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                    </div>
                    <h3><?php esc_html_e('Popup Builder', 'primekit-addons'); ?></h3>
                    <p><?php esc_html_e('Create beautiful popups and modals with Elementor.', 'primekit-addons'); ?></p>
                </div>
            </div>

            <div class="pkwiz-actions">
                <a href="<?php echo esc_url($this->step_url('features')); ?>" class="pkwiz-btn pkwiz-btn--primary">
                    <?php esc_html_e('Get Started', 'primekit-addons'); ?>
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h12M12 5l5 5-5 5"/></svg>
                </a>
            </div>
        </div>

        <?php elseif ($step === 'features'): ?>
        <!-- ─── STEP 2: FEATURES ─────────────────────────────── -->
        <div class="pkwiz-content">
            <h1 class="pkwiz-heading"><?php esc_html_e('Configure Features', 'primekit-addons'); ?></h1>
            <p class="pkwiz-subheading">
                <?php esc_html_e('Enable the features you need. You can change these anytime from PrimeKit Settings.', 'primekit-addons'); ?>
            </p>

            <form class="pkwiz-feature-form" id="pkwiz-feature-form">
                <div class="pkwiz-toggle-list">

                    <!-- Theme Builder -->
                    <label class="pkwiz-toggle-item" for="pkwiz-themebuilder">
                        <div class="pkwiz-toggle-item__icon pkwiz-icon--theme">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="pkwiz-toggle-item__text">
                            <strong><?php esc_html_e('Theme Builder', 'primekit-addons'); ?></strong>
                            <span><?php esc_html_e('Build custom headers, footers, single posts, archives and WooCommerce layouts.', 'primekit-addons'); ?></span>
                        </div>
                        <div class="pkwiz-toggle-item__switch">
                            <input type="checkbox"
                                   id="pkwiz-themebuilder"
                                   name="features[]"
                                   value="enable_themebuilder"
                                   <?php checked(!empty($features['enable_themebuilder'])); ?>>
                            <span class="pkwiz-switch" aria-hidden="true"></span>
                        </div>
                    </label>

                    <!-- Template Importer -->
                    <label class="pkwiz-toggle-item" for="pkwiz-template-import">
                        <div class="pkwiz-toggle-item__icon pkwiz-icon--templates">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
                        </div>
                        <div class="pkwiz-toggle-item__text">
                            <strong><?php esc_html_e('Template Importer', 'primekit-addons'); ?></strong>
                            <span><?php esc_html_e('Browse and import ready-made templates directly inside the Elementor editor.', 'primekit-addons'); ?></span>
                        </div>
                        <div class="pkwiz-toggle-item__switch">
                            <input type="checkbox"
                                   id="pkwiz-template-import"
                                   name="features[]"
                                   value="enable_editor_template_import"
                                   <?php checked(!empty($features['enable_editor_template_import'])); ?>>
                            <span class="pkwiz-switch" aria-hidden="true"></span>
                        </div>
                    </label>

                </div><!-- /.pkwiz-toggle-list -->

                <div class="pkwiz-actions">
                    <a href="<?php echo esc_url($this->step_url('welcome')); ?>" class="pkwiz-btn pkwiz-btn--secondary">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 10H4M8 5L3 10l5 5"/></svg>
                        <?php esc_html_e('Back', 'primekit-addons'); ?>
                    </a>
                    <button type="submit" class="pkwiz-btn pkwiz-btn--primary" id="pkwiz-save-features">
                        <?php esc_html_e('Save & Continue', 'primekit-addons'); ?>
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h12M12 5l5 5-5 5"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <?php elseif ($step === 'done'): ?>
        <!-- ─── STEP 3: DONE ─────────────────────────────── -->
        <div class="pkwiz-content pkwiz-content--done">
            <div class="pkwiz-done-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="11" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M7 12.5l3.5 3.5 6.5-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="pkwiz-heading"><?php esc_html_e('You\'re All Set!', 'primekit-addons'); ?></h1>
            <p class="pkwiz-subheading">
                <?php esc_html_e('PrimeKit Addons is configured and ready to use. Start building amazing pages with Elementor!', 'primekit-addons'); ?></p>

            <div class="pkwiz-done-links">
                <a href="<?php echo esc_url(admin_url('admin.php?page=primekit_home')); ?>" class="pkwiz-done-link">
                    <div class="pkwiz-done-link__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    </div>
                    <span><?php esc_html_e('Dashboard', 'primekit-addons'); ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=primekit_available_widgets')); ?>" class="pkwiz-done-link">
                    <div class="pkwiz-done-link__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>
                    </div>
                    <span><?php esc_html_e('Manage Widgets', 'primekit-addons'); ?></span>
                </a>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=primekit_library')); ?>" class="pkwiz-done-link">
                    <div class="pkwiz-done-link__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <span><?php esc_html_e('Template Library', 'primekit-addons'); ?></span>
                </a>
                <a href="https://primekitaddons.com/docs/" target="_blank" rel="noopener noreferrer" class="pkwiz-done-link">
                    <div class="pkwiz-done-link__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <span><?php esc_html_e('Documentation', 'primekit-addons'); ?></span>
                </a>
            </div>

            <div class="pkwiz-actions">
                <a href="<?php echo esc_url(admin_url('admin.php?page=primekit_home')); ?>" class="pkwiz-btn pkwiz-btn--primary pkwiz-btn--large">
                    <?php esc_html_e('Go to Dashboard', 'primekit-addons'); ?>
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 10h12M12 5l5 5-5 5"/></svg>
                </a>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div><!-- /.pkwiz-wrap -->

<?php wp_print_scripts(); ?>
</body>
</html>
