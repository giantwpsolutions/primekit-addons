<script setup>
import { ref, computed, onMounted } from 'vue'
import { api } from '../api/index.js'
import { widgetTotal } from '../store/widgets.js'

const activeTab = ref('all')
const searchQuery = ref('')
const loading = ref(true)
const saving = ref(null)
const toast = ref(null)

const iconsBase = window.primekitAdmin?.iconsUrl || ''

const CAT_ORDER = ['General', 'Post & Blog', 'Media & Animation', 'Navigation', 'Forms & Integration', 'Site Elements', 'WooCommerce']

const allWidgets = ref({
  free: [
    { key: 'primekit_anim_text_widget_field',      label: 'Animated Text',          icon: 'advanced-animated-text.svg',    cat: 'General',             enabled: true },
    { key: 'primekit_blockquote_widget_field',      label: 'Blockquote',             icon: 'blockquote.svg',                cat: 'General',             enabled: true },
    { key: 'primekit_sec_title_widget_field',       label: 'Section Title',          icon: 'section-title.svg',             cat: 'General',             enabled: true },
    { key: 'primekit_sticker_text_field',           label: 'Sticker Text',           icon: 'sticker-text.svg',              cat: 'General',             enabled: true },
    { key: 'primekit_cta_widget_field',             label: 'Call To Action',         icon: 'call-to-action.svg',            cat: 'General',             enabled: true },
    { key: 'primekit_icon_box_widget_field',        label: 'Icon Box',               icon: 'icon-box.svg',                  cat: 'General',             enabled: true },
    { key: 'primekit_flip_box_widget_field',        label: 'Flip Box',               icon: 'flip-box.svg',                  cat: 'General',             enabled: true },
    { key: 'primekit_card_info_widget_field',       label: 'Card Info',              icon: 'card-info.svg',                 cat: 'General',             enabled: true },
    { key: 'primekit_dual_button_widget_field',     label: 'Dual Button',            icon: 'dual-button.svg',               cat: 'General',             enabled: true },
    { key: 'primekit_pricing_table_widget_field',   label: 'Pricing Table',          icon: 'advanced-pricing-table.svg',    cat: 'General',             enabled: true },
    { key: 'primekit_counter_up_widget_field',      label: 'Counter Up',             icon: 'counter-up.svg',                cat: 'General',             enabled: true },
    { key: 'primekit_count_down_widget_field',      label: 'Count Down Timer',       icon: 'count-down-timer.svg',          cat: 'General',             enabled: true },
    { key: 'primekit_circular_skill_widget_field',  label: 'Circular Skill',         icon: 'advanced-circular-skill.svg',   cat: 'General',             enabled: true },
    { key: 'primekit_skill_bar_widget_field',       label: 'Skill Bar',              icon: 'skill-bar.svg',                 cat: 'General',             enabled: true },
    { key: 'primekit_testi_caro_widget_field',      label: 'Testimonial Carousel',   icon: 'testimonial-carousel.svg',      cat: 'General',             enabled: true },
    { key: 'primekit_team_member_widget_field',     label: 'Team Member',            icon: 'team-member.svg',               cat: 'General',             enabled: true },
    { key: 'primekit_social_share_widget_field',    label: 'Social Share',           icon: 'social-share.svg',              cat: 'General',             enabled: true },
    { key: 'primekit_business_hours_field',         label: 'Business Hours',         icon: 'business-hours.svg',            cat: 'General',             enabled: true },
    { key: 'primekit_portfolio_widget_field',       label: 'Portfolio',              icon: 'portfolio.svg',                 cat: 'General',             enabled: true },
    { key: 'primekit_cost_estimation_field',        label: 'Cost Estimation',        icon: 'cost-estimation.svg',           cat: 'General',             enabled: true },
    { key: 'primekit_advanced_list_field',          label: 'Advanced List',          icon: 'advance-list.svg',              cat: 'General',             enabled: true },
    { key: 'primekit_glass_card_field',             label: 'Glass Card',             icon: 'glass-card.svg',                cat: 'General',             enabled: true },

    { key: 'primekit_blog_fancy_widget_field',      label: 'Blog Fancy',             icon: 'fancy-blog-posts.svg',          cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_blog_grid_widget_field',       label: 'Blog Grid',              icon: 'blog-posts-grid.svg',           cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_blog_list_widget_field',       label: 'Blog List',              icon: 'blog-posts-list.svg',           cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_author_bio_widget_field',      label: 'Author Bio',             icon: 'post-author-bio.svg',           cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_post_content_widget_field',    label: 'Post Content',           icon: 'post-content.svg',              cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_post_meta_widget_field',       label: 'Post Meta',              icon: 'post-meta-info.svg',            cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_post_title_widget_field',      label: 'Post Title',             icon: 'post-title.svg',                cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_feat_img_widget_field',        label: 'Featured Image',         icon: 'featured-image.svg',            cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_recent_post_widget_field',     label: 'Recent Posts',           icon: 'recent-posts-list.svg',         cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_related_post_widget_field',    label: 'Related Posts',          icon: 'related-posts.svg',             cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_cat_list_widget_field',        label: 'Category List',          icon: 'post-category-list.svg',        cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_tag_info_widget_field',        label: 'Post Tag Info',          icon: 'post-tag-info.svg',             cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_archive_title_field',          label: 'Archive Title',          icon: 'archive-title.svg',             cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_modern_post_grid_field',       label: 'Modern Post Grid',       icon: 'modern-post-grid.svg',          cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_popular_posts_field',          label: 'Popular Posts',          icon: 'popular-post.svg',              cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_fetch_posts_field',            label: 'Fetch Posts',            icon: 'fetch-posts.svg',               cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_posts_slider_field',           label: 'Posts Slider',           icon: 'post-slider.svg',               cat: 'Post & Blog',         enabled: true },
    { key: 'primekit_comment_form_widget_field',    label: 'Comment Form',           icon: 'comment-form.svg',              cat: 'Post & Blog',         enabled: true },

    { key: 'primekit_before_after_widget_field',    label: 'Before After Image',     icon: 'before-after-image.svg',        cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_img_hover_widget_field',       label: 'Image Hover',            icon: 'image-hover.svg',               cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_image_gallery_field',          label: 'Image Gallery',          icon: 'image-gallery.svg',             cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_img_text_scroll_widget_field', label: 'Image & Text Scroll',    icon: 'image-and-text-scroll.svg',     cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_single_img_scroll_field',      label: 'Single Image Scroll',    icon: 'single-image-scroll.svg',       cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_lottie_icon_widget_field',     label: 'Lottie Animation',       icon: 'lottie.svg',                    cat: 'Media & Animation',   enabled: true },
    { key: 'primekit_shape_anim_widget_field',      label: 'Animated Shape',         icon: 'animated-shape.svg',            cat: 'Media & Animation',   enabled: true },

    { key: 'primekit_breadcrumb_widget_field',      label: 'Breadcrumb',             icon: 'breadcrumb.svg',                cat: 'Navigation',          enabled: true },
    { key: 'primekit_wp_menu_widget_field',         label: 'WordPress Menu',         icon: 'wordpress-menu.svg',            cat: 'Navigation',          enabled: true },
    { key: 'primekit_search_form_widget_field',     label: 'Search Form',            icon: 'search-form.svg',               cat: 'Navigation',          enabled: true },
    { key: 'primekit_search_icon_widget_field',     label: 'Search Icon',            icon: 'search-icon.svg',               cat: 'Navigation',          enabled: true },
    { key: 'primekit_back_top_widget_field',        label: 'Back To Top',            icon: 'back-to-top-button.svg',        cat: 'Navigation',          enabled: true },
    { key: 'primekit_call_button_widget_field',     label: 'Sticky Call Button',     icon: 'call-icon.svg',                 cat: 'Navigation',          enabled: true },

    { key: 'primekit_contact_form7_widget_field',   label: 'Contact Form 7',         icon: 'contact-form-7.svg',            cat: 'Forms & Integration', enabled: true },
    { key: 'primekit_gravity_form_field',           label: 'Gravity Form',           icon: 'gravity-form.svg',              cat: 'Forms & Integration', enabled: true },
    { key: 'primekit_mailchimp_switch_field',       label: 'MailChimp',              icon: 'mailchimp.svg',                 cat: 'Forms & Integration', enabled: true },
    { key: 'primekit_contact_info_widget_field',    label: 'Contact & Social Info',  icon: 'contact-and-social-info.svg',   cat: 'Forms & Integration', enabled: true },

    { key: 'primekit_site_logo_widget_field',       label: 'Site Logo',              icon: 'site-logo.svg',                 cat: 'Site Elements',       enabled: true },
    { key: 'primekit_site_title_tagline_field',     label: 'Site Title & Tagline',   icon: 'site-title-and-tagline.svg',    cat: 'Site Elements',       enabled: true },
    { key: 'primekit_page_title_widget_field',      label: 'Page Title',             icon: 'page-title.svg',                cat: 'Site Elements',       enabled: true },
    { key: 'primekit_page_content_widget_field',    label: 'Page Content',           icon: 'page-content.svg',              cat: 'Site Elements',       enabled: true },
    { key: 'primekit_copyright_field',              label: 'Copyright',              icon: 'copyright.svg',                 cat: 'Site Elements',       enabled: true },
    { key: 'primekit_template_slider_field',        label: 'Template Slider',        icon: 'template-slider.svg',           cat: 'Site Elements',       enabled: true },
    { key: 'primekit_loading_screen_widget_field',  label: 'Loading Screen',         icon: 'loading-screen.svg',            cat: 'Site Elements',       enabled: true },
    { key: 'primekit_popup_widget_field',           label: 'Popup',                  icon: 'popup.svg',                     cat: 'Site Elements',       enabled: true },
  ],
  woo: [
    { key: 'primekit_wc_add_to_cart_field',      label: 'Product Add To Cart',          icon: 'add-to-cart.svg',                  cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_cart_icon_field',        label: 'Product Cart Icon',            icon: 'cart-icon.svg',                    cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_cart_page_field',        label: 'Product Cart Page',            icon: 'cart-page.svg',                    cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_checkout_field',         label: 'Product Checkout',             icon: 'checkout-page.svg',                cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_product_image_field',    label: 'Product Image',                icon: 'product-image.svg',                cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_product_meta_field',     label: 'Product Meta',                 icon: 'product-meta.svg',                 cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_product_price_field',    label: 'Product Price',                icon: 'product-pricing.svg',              cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_related_field',          label: 'Product Related',              icon: 'related-product.svg',              cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_short_desc_field',       label: 'Product Short Description',    icon: 'product-short-description.svg',    cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_product_tabs_field',     label: 'Product Tabs',                 icon: 'product-tabs-data.svg',            cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_product_title_field',    label: 'Product Title',                icon: 'product-title.svg',                cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_account_field',          label: 'WooCommerce Account',          icon: 'my-account.svg',                   cat: 'WooCommerce', enabled: true },
    { key: 'primekit_wc_breadcrumb_field',       label: 'WooCommerce Breadcrumb',       icon: 'WooCommerce-breadcrumb.svg',       cat: 'WooCommerce', enabled: true },
  ],
  pro: [
    { key: 'primekit_advanced_accordion_locked_widget_field',       label: 'Advanced Accordion',       icon: 'advance-accordion.svg',            cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_advanced_pricing_table_locked_widget_field',   label: 'Advanced Pricing Table',   icon: 'advanced-pricing-table.svg',       cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_advanced_tab_locked_widget_field',             label: 'Advanced Tabs',            icon: 'advanced-tab.svg',                 cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_email_signature_locked_widget_field',          label: 'Email Signature',          icon: 'email-signature.svg',              cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_jobs_locked_widget_field',                     label: 'Jobs',                     icon: 'jobs.svg',                         cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_project_progress_track_locked_widget_field',   label: 'Project Progress Tracker', icon: 'project-progress-tracker.svg',     cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_resources_locked_widget_field',                label: 'Resources',                icon: 'resources.svg',                    cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_revenue_growth_graphs_locked_widget_field',    label: 'Revenue Growth Graph',     icon: 'revenue-growth-graphs.svg',        cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_team_member_carousel_locked_widget_field',     label: 'Team Member Carousel',     icon: 'team-member-carousel.svg',         cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_timeline_milestone_locked_widget_field',       label: 'Timeline Milestone',       icon: 'timeline-milestone.svg',           cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_video_testimonials_locked_widget_field',       label: 'Video Testimonials',       icon: 'video-testimonials.svg',           cat: 'General',             enabled: false, pro: true },
    { key: 'primekit_lottie_locked_widget_field',                   label: 'Lottie Animation',         icon: 'lottie.svg',                       cat: 'Media & Animation',   enabled: false, pro: true },
    { key: 'primekit_resource_form_locked_widget_field',            label: 'Resource Form',            icon: 'resource-form.svg',                cat: 'Forms & Integration', enabled: false, pro: true },
    { key: 'primekit_whatsapp_chat_locked_widget_field',            label: 'WhatsApp Chat',            icon: 'WhatsApps-Chat.svg',               cat: 'Forms & Integration', enabled: false, pro: true },
    { key: 'primekit_protected_content_locked_widget_field',        label: 'Protected Content',        icon: 'protected-contents.svg',           cat: 'Site Elements',       enabled: false, pro: true },
  ],
})

widgetTotal.value = allWidgets.value.free.length + allWidgets.value.woo.length + allWidgets.value.pro.length

onMounted(async () => {
  try {
    const res = await api.getWidgets()
    if (res?.widgets) {
      for (const tab of ['free', 'woo', 'pro']) {
        allWidgets.value[tab] = allWidgets.value[tab].map(w => ({
          ...w,
          enabled: res.widgets[w.key] !== undefined ? Boolean(res.widgets[w.key]) : w.enabled,
        }))
      }
    }
  } catch { /* use defaults */ } finally {
    loading.value = false
  }
})

const tabList = computed(() => {
  const f = allWidgets.value.free
  const w = allWidgets.value.woo
  const p = allWidgets.value.pro
  return [
    { key: 'all',  label: 'All',  count: f.length + w.length + p.length },
    { key: 'free', label: 'Free', count: f.length + w.length },
    { key: 'pro',  label: 'Pro',  count: p.length },
  ]
})

const tabWidgets = computed(() => {
  const { free, woo, pro } = allWidgets.value
  if (activeTab.value === 'all')  return [...free, ...woo, ...pro]
  if (activeTab.value === 'free') return [...free, ...woo]
  return [...pro]
})

const currentWidgets = computed(() => {
  if (!searchQuery.value) return tabWidgets.value
  const q = searchQuery.value.toLowerCase()
  return tabWidgets.value.filter(w => w.label.toLowerCase().includes(q))
})

const activeCount = computed(() => tabWidgets.value.filter(w => w.enabled && !w.pro).length)
const totalCount  = computed(() => tabWidgets.value.filter(w => !w.pro).length)

const groupedWidgets = computed(() => {
  const map = {}
  for (const w of currentWidgets.value) {
    const cat = w.cat || 'General'
    if (!map[cat]) map[cat] = []
    map[cat].push(w)
  }
  return CAT_ORDER.filter(c => map[c]).map(c => ({ label: c, widgets: map[c] }))
})

async function toggleWidget(widget) {
  if (widget.pro) return
  saving.value = widget.key
  const prev = widget.enabled
  widget.enabled = !widget.enabled
  try {
    await api.toggleWidget(widget.key, widget.enabled)
    showToast(`${widget.label} ${widget.enabled ? 'enabled' : 'disabled'}`)
  } catch {
    widget.enabled = prev
    showToast('Failed to save. Please try again.', 'error')
  } finally {
    saving.value = null
  }
}

async function enableAll() {
  const targets = tabWidgets.value.filter(w => !w.pro && !w.enabled)
  targets.forEach(w => (w.enabled = true))
  await Promise.all(targets.map(w =>
    api.toggleWidget(w.key, true).catch(() => (w.enabled = false))
  ))
}

async function disableAll() {
  const targets = tabWidgets.value.filter(w => !w.pro && w.enabled)
  targets.forEach(w => (w.enabled = false))
  await Promise.all(targets.map(w =>
    api.toggleWidget(w.key, false).catch(() => (w.enabled = true))
  ))
}

let toastTimer = null
function showToast(msg, type = 'success') {
  toast.value = { msg, type }
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => toast.value = null, 2500)
}
</script>

<template>
  <div class="pk-space-y-5">
    <!-- Header -->
    <div class="pk-flex pk-items-center pk-justify-between">
      <div>
        <h2 class="pk-text-lg pk-font-bold pk-text-gray-800 pk-m-0">Widgets</h2>
        <p class="pk-text-sm pk-text-gray-500 pk-m-0 pk-mt-0.5">Enable or disable individual Elementor widgets</p>
      </div>
      <div class="pk-flex pk-items-center pk-gap-2">
        <button @click="disableAll"
                class="pk-px-3 pk-py-1.5 pk-text-sm pk-rounded-lg pk-border pk-border-gray-200 pk-text-gray-600 pk-bg-white pk-transition-colors"
                style="cursor:pointer"
                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
          Disable All
        </button>
        <button @click="enableAll"
                class="pk-px-3 pk-py-1.5 pk-text-sm pk-rounded-lg pk-text-white pk-transition-colors"
                style="background:#6c63ff;cursor:pointer;border:none"
                onmouseover="this.style.background='#5a52e0'" onmouseout="this.style.background='#6c63ff'">
          Enable All
        </button>
      </div>
    </div>

    <!-- Tabs + Search -->
    <div class="pk-flex pk-items-center pk-justify-between pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-px-5 pk-py-3">
      <div class="pk-flex pk-gap-1">
        <button v-for="tab in tabList" :key="tab.key"
          @click="activeTab = tab.key; searchQuery = ''"
          class="pk-flex pk-items-center pk-gap-2 pk-px-3 pk-py-1.5 pk-rounded-lg pk-text-sm pk-font-medium pk-transition-all"
          :style="activeTab === tab.key
            ? 'background:rgba(108,99,255,0.1);color:#6c63ff;border:none;cursor:pointer'
            : 'background:transparent;color:#6b7280;border:none;cursor:pointer'"
        >
          {{ tab.label }}
          <span class="pk-text-xs pk-px-1.5 pk-py-0.5 pk-rounded"
                :style="activeTab === tab.key ? 'background:#6c63ff;color:white' : 'background:#f3f4f6;color:#6b7280'">
            {{ tab.count }}
          </span>
        </button>
      </div>
      <div class="pk-relative">
        <svg class="pk-absolute pk-left-3 pk-top-1/2 pk-w-4 pk-h-4 pk-text-gray-400"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transform:translateY(-50%)">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input v-model="searchQuery" type="text" placeholder="Search widgets..."
               class="pk-pl-9 pk-pr-4 pk-py-1.5 pk-text-sm pk-border pk-border-gray-200 pk-rounded-lg pk-outline-none"
               style="width:200px;background:#f9fafb;color:#374151"
               onfocus="this.style.borderColor='#6c63ff';this.style.background='white'"
               onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb'" />
      </div>
    </div>

    <!-- Active count bar -->
    <div class="pk-flex pk-items-center pk-gap-2 pk-px-1">
      <span class="pk-text-sm pk-text-gray-500">
        <strong class="pk-text-gray-800">{{ activeCount }}</strong> of {{ totalCount }} active
      </span>
      <div class="pk-flex-1 pk-h-1.5 pk-bg-gray-100 pk-rounded-full pk-max-w-xs">
        <div class="pk-h-1.5 pk-rounded-full pk-transition-all" style="background:#6c63ff"
             :style="`width:${totalCount ? Math.round((activeCount/totalCount)*100) : 0}%`"></div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="pk-flex pk-items-center pk-justify-center pk-py-16">
      <div class="pk-w-8 pk-h-8 pk-rounded-full pk-border-2 pk-border-t-transparent pk-animate-spin"
           style="border-color:#6c63ff;border-top-color:transparent"></div>
    </div>

    <!-- Grouped widget sections -->
    <div v-else class="pk-space-y-6">

      <!-- Empty state -->
      <div v-if="!currentWidgets.length" class="pk-py-12 pk-text-center pk-text-gray-400">
        <p class="pk-text-sm">No widgets found for "{{ searchQuery }}"</p>
      </div>

      <div v-for="group in groupedWidgets" :key="group.label">
        <!-- Category header -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
          <h3 style="margin:0;font-size:13px;font-weight:600;color:#6c63ff;white-space:nowrap;">
            {{ group.label }}
          </h3>
          <div style="flex:1;height:1px;background:#e5e7eb;"></div>
          <span style="font-size:11px;color:#9ca3af;white-space:nowrap;">{{ group.widgets.length }} widgets</span>
        </div>

        <!-- Widget list -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:8px;">
          <div v-for="widget in group.widgets" :key="widget.key"
               style="position:relative;overflow:hidden;display:flex;align-items:center;gap:12px;padding:10px 14px;background:white;border-radius:10px;border:1px solid #e9e9f0;transition:border-color 0.15s;"
               :style="widget.enabled && !widget.pro ? 'border-color:#dddcf7' : 'border-color:#e9e9f0'">

            <!-- PRO ribbon -->
            <div v-if="widget.pro"
                 style="position:absolute;top:11px;right:-17px;width:68px;text-align:center;padding:3px 0;transform:rotate(45deg);background:#e53935;color:white;font-size:9px;font-weight:500;letter-spacing:0.5px;">
              PRO
            </div>

            <!-- Icon -->
            <img :src="iconsBase + widget.icon"
                 :alt="widget.label"
                 width="36" height="36"
                 :style="`display:block;flex-shrink:0;width:36px;height:36px;object-fit:contain;${!widget.enabled && !widget.pro ? 'opacity:0.35;filter:grayscale(1)' : ''}`" />

            <!-- Name -->
            <span style="flex:1;font-size:13px;font-weight:500;line-height:1.3;"
                  :style="widget.enabled && !widget.pro ? 'color:#2d2b4e' : 'color:#9ca3af'">
              {{ widget.label }}
            </span>

            <!-- Toggle -->
            <label v-if="!widget.pro" class="pk-toggle" style="cursor:pointer;flex-shrink:0;">
              <input type="checkbox" :checked="widget.enabled"
                     :disabled="saving === widget.key"
                     @change="toggleWidget(widget)" />
              <span class="pk-toggle-slider"></span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <Transition name="pk-fade">
      <div v-if="toast"
           class="pk-fixed pk-bottom-6 pk-right-6 pk-px-4 pk-py-3 pk-rounded-xl pk-shadow-lg pk-text-sm pk-font-medium pk-text-white pk-z-50"
           :style="toast.type === 'error' ? 'background:#ef4444' : 'background:#6c63ff'">
        {{ toast.msg }}
      </div>
    </Transition>
  </div>
</template>
