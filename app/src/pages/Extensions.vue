<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../api/index.js'
import { __ } from '../utils/i18n.js'

const saving = ref(false)
const saved = ref(false)
const loading = ref(true)

const extensions = ref({
  enable_themebuilder: true,
  enable_editor_template_import: true,
  enable_custom_css: true,
  enable_css_transform: true,
  enable_preloader: true,
  enable_wrapper_url: true,
  enable_nested_tabs: true,
})

const items = [
  {
    key: 'enable_themebuilder',
    label: __('Theme Builder'),
    desc: __('Build custom headers, footers, single post/page templates, archive pages, 404, and WooCommerce templates.'),
    icon: 'layout',
  },
  {
    key: 'enable_editor_template_import',
    label: __('Template Library'),
    desc: __('Access 20+ ready-to-import templates directly from the Elementor editor panel.'),
    icon: 'book-open',
  },
  {
    key: 'enable_custom_css',
    label: __('Custom CSS'),
    desc: __('Add per-element custom CSS from within the Elementor panel.'),
    icon: 'code',
  },
  {
    key: 'enable_css_transform',
    label: __('CSS Transform Controls'),
    desc: __('Control rotate, scale, skew, and translate properties for any Elementor widget.'),
    icon: 'sliders',
  },
  {
    key: 'enable_preloader',
    label: __('Page PreLoader'),
    desc: __('Add an animated loading screen that displays while your page content loads.'),
    icon: 'loader',
  },
  {
    key: 'enable_wrapper_url',
    label: __('Wrapper Link'),
    desc: __('Make any Elementor container or section fully clickable with a custom URL.'),
    icon: 'link',
  },
  {
    key: 'enable_nested_tabs',
    label: __('Enhanced Nested Tabs'),
    desc: __("Extended functionality for Elementor's nested tabs widget with extra style controls."),
    icon: 'layers',
  },
]

onMounted(async () => {
  try {
    const res = await api.getExtensions()
    if (res) Object.assign(extensions.value, res)
  } catch { /* use defaults */ } finally {
    loading.value = false
  }
})

async function save() {
  saving.value = true
  try {
    await api.saveExtensions(extensions.value)
    saved.value = true
    setTimeout(() => saved.value = false, 2000)
  } catch (e) {
    alert(__('Failed to save settings.'))
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="pk-space-y-5">
    <div class="pk-flex pk-items-center pk-justify-between">
      <div>
        <h2 class="pk-text-lg pk-font-bold pk-text-gray-800 pk-m-0">{{ __('Extensions') }}</h2>
        <p class="pk-text-sm pk-text-gray-500 pk-m-0 pk-mt-0.5">{{ __('Global Elementor feature extensions') }}</p>
      </div>
      <button @click="save" :disabled="saving"
              class="pk-flex pk-items-center pk-gap-2 pk-px-4 pk-py-2 pk-rounded-lg pk-text-sm pk-font-medium pk-text-white pk-transition-all"
              style="border:none;cursor:pointer"
              :style="saved ? 'background:#10b981' : 'background:#6c63ff'">
        <svg v-if="saved" class="pk-w-4 pk-h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ saved ? __('Saved!') : saving ? __('Saving…') : __('Save Changes') }}
      </button>
    </div>

    <div v-if="loading" class="pk-flex pk-items-center pk-justify-center pk-py-16">
      <div class="pk-w-8 pk-h-8 pk-rounded-full pk-border-2 pk-animate-spin" style="border-color:#6c63ff;border-top-color:transparent"></div>
    </div>

    <div v-else class="pk-grid pk-gap-3" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr))">
      <div v-for="item in items" :key="item.key"
           class="pk-bg-white pk-rounded-xl pk-border pk-p-5 pk-transition-all"
           :style="extensions[item.key] ? 'border-color:#e9e7ff' : 'border-color:#f3f4f6'">
        <div class="pk-flex pk-items-start pk-justify-between pk-gap-3">
          <div class="pk-flex pk-items-start pk-gap-3">
            <div class="pk-w-10 pk-h-10 pk-rounded-xl pk-flex pk-items-center pk-justify-center pk-flex-shrink-0"
                 :style="extensions[item.key] ? 'background:rgba(108,99,255,0.1)' : 'background:#f3f4f6'">
              <svg class="pk-w-5 pk-h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                   :style="extensions[item.key] ? 'color:#6c63ff' : 'color:#9ca3af'">
                <template v-if="item.icon==='layout'">
                  <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
                </template>
                <template v-else-if="item.icon==='book-open'">
                  <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>
                </template>
                <template v-else-if="item.icon==='code'">
                  <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
                </template>
                <template v-else-if="item.icon==='sliders'">
                  <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                  <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                  <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                  <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
                </template>
                <template v-else-if="item.icon==='loader'">
                  <line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/>
                  <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/>
                  <line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/>
                  <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/>
                </template>
                <template v-else-if="item.icon==='link'">
                  <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
                </template>
                <template v-else-if="item.icon==='layers'">
                  <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                  <polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>
                </template>
              </svg>
            </div>
            <div>
              <p class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">{{ item.label }}</p>
              <p class="pk-text-xs pk-text-gray-500 pk-m-0 pk-mt-1 pk-leading-relaxed">{{ item.desc }}</p>
            </div>
          </div>
          <label class="pk-toggle pk-flex-shrink-0">
            <input type="checkbox" v-model="extensions[item.key]" />
            <span class="pk-toggle-slider"></span>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>
