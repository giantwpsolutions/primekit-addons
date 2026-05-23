<script setup>
import { ref, onMounted, computed } from 'vue'
import { api } from '../api/index.js'
import { __ } from '../utils/i18n.js'

const loading = ref(true)
const data = ref(null)
const performanceMode = ref('standard')

// Fallback/mock data so it renders even before API is ready
const mockData = {
  version: window.primekitAdmin?.version || '2.0.0',
  totalWidgets: 70,
  activeWidgets: 51,
  inactiveWidgets: 19,
  proWidgets: 11,
  freeWidgets: 54,
  wooWidgets: 5,
  changelog: [
    { type: 'new', label: __('NEW WIDGET'), text: 'Glass Card — frosted glass container with backdrop-blur & border-glow effects' },
    { type: 'new', label: __('NEW WIDGET'), text: 'Sticker Text — animated sticky label element ideal for promotions and highlights' },
    { type: 'new', label: __('NEW WIDGET'), text: 'Sticky Call Button — floating click-to-call button with pulse animation' },
    { type: 'improvement', label: __('IMPROVEMENT'), text: 'Before After Image — added touch/swipe support for mobile devices' },
    { type: 'improvement', label: __('IMPROVEMENT'), text: 'Testimonial Carousel — smooth CSS transitions, 4 new layout skins added' },
    { type: 'improvement', label: __('IMPROVEMENT'), text: 'Pricing Table — new ribbon style and highlight column support' },
    { type: 'fix', label: __('FIX'), text: 'Sticky Call Button z-index conflict with Elementor popup overlay' },
    { type: 'fix', label: __('FIX'), text: 'Countdown Timer accuracy drift on heavily cached WordPress pages' },
  ],
}

onMounted(async () => {
  try {
    const res = await api.getDashboard()
    data.value = { ...mockData, ...res }
    performanceMode.value = res.performanceMode || 'standard'
  } catch {
    data.value = mockData
  } finally {
    loading.value = false
  }
})

const d = computed(() => data.value || mockData)

const tagColor = {
  new: { bg: 'rgba(16,185,129,0.12)', color: '#059669', border: 'rgba(16,185,129,0.25)' },
  improvement: { bg: 'rgba(59,130,246,0.12)', color: '#2563eb', border: 'rgba(59,130,246,0.25)' },
  fix: { bg: 'rgba(239,68,68,0.12)', color: '#dc2626', border: 'rgba(239,68,68,0.25)' },
}

const freePercent = computed(() => d.value.totalWidgets ? Math.round((d.value.freeWidgets / d.value.totalWidgets) * 100) : 0)
const proPercent = computed(() => d.value.totalWidgets ? Math.round((d.value.proWidgets / d.value.totalWidgets) * 100) : 0)
const wooPercent = computed(() => d.value.totalWidgets ? Math.round((d.value.wooWidgets / d.value.totalWidgets) * 100) : 0)
</script>

<template>
  <div class="pk-space-y-6">
    <!-- Hero Banner -->
    <div class="pk-hero-gradient pk-rounded-2xl pk-p-8 pk-relative pk-overflow-hidden" style="min-height:160px">
      <div class="pk-relative pk-z-10">
        <p class="pk-text-xs pk-font-semibold pk-tracking-widest pk-mb-2 pk-uppercase" style="color:rgba(255,255,255,0.65)">{{ __('WELCOME BACK') }}</p>
        <h2 class="pk-text-2xl pk-font-bold pk-text-white pk-mb-2" style="margin:0 0 8px">{{ __('PrimeKit Addons Dashboard') }}</h2>
        <p class="pk-text-sm pk-mb-5" style="color:rgba(255,255,255,0.75);margin:0 0 20px">
          <strong style="color:white">{{ d.activeWidgets }} {{ __('widgets active') }}</strong> {{ __('out of') }} {{ d.totalWidgets }} {{ __('total.') }}
          {{ __('Run the setup wizard to optimise your configuration.') }}
        </p>
        <div class="pk-flex pk-gap-3">
          <button class="pk-flex pk-items-center pk-gap-2 pk-px-4 pk-py-2 pk-rounded-lg pk-text-sm pk-font-medium pk-transition-all"
                  style="background:white;color:#6c63ff;border:none;cursor:pointer">
            <svg class="pk-w-3.5 pk-h-3.5" fill="currentColor" viewBox="0 0 24 24">
              <polygon points="5 3 19 12 5 21 5 3"/>
            </svg>
            {{ __('Setup Wizard') }}
          </button>
          <button class="pk-flex pk-items-center pk-gap-2 pk-px-4 pk-py-2 pk-rounded-lg pk-text-sm pk-font-medium pk-transition-all"
                  style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);cursor:pointer">
            {{ __('View Changelog') }} →
          </button>
        </div>
      </div>
      <!-- Lightning bolt decoration -->
      <div class="pk-absolute pk-right-8 pk-top-1/2 pk--translate-y-1/2 pk-text-right" style="transform:translateY(-50%)">
        <div style="font-size:72px;line-height:1;opacity:0.35;filter:drop-shadow(0 0 20px rgba(255,255,255,0.5))">⚡</div>
        <div class="pk-text-xs pk-font-semibold pk-mt-1" style="color:rgba(255,255,255,0.6)">v{{ d.version }}</div>
      </div>
    </div>

    <!-- Stats row -->
    <div class="pk-grid pk-grid-cols-4 pk-gap-4">
      <div class="pk-stat-card pk-bg-white pk-rounded-xl pk-p-5 pk-border pk-border-gray-100">
        <div class="pk-text-3xl pk-font-bold pk-mb-1" style="color:#6c63ff">{{ d.totalWidgets }}</div>
        <div class="pk-text-sm pk-font-medium pk-text-gray-700">{{ __('Total Widgets') }}</div>
        <div class="pk-text-xs pk-text-gray-400 pk-mt-0.5">{{ __('across all types') }}</div>
      </div>
      <div class="pk-stat-card pk-bg-white pk-rounded-xl pk-p-5 pk-border pk-border-gray-100">
        <div class="pk-text-3xl pk-font-bold pk-mb-1" style="color:#10b981">{{ d.activeWidgets }}</div>
        <div class="pk-text-sm pk-font-medium pk-text-gray-700">{{ __('Active') }}</div>
        <div class="pk-text-xs pk-text-gray-400 pk-mt-0.5">{{ __('currently loaded') }}</div>
      </div>
      <div class="pk-stat-card pk-bg-white pk-rounded-xl pk-p-5 pk-border pk-border-gray-100">
        <div class="pk-text-3xl pk-font-bold pk-mb-1 pk-text-gray-400">{{ d.inactiveWidgets }}</div>
        <div class="pk-text-sm pk-font-medium pk-text-gray-700">{{ __('Inactive') }}</div>
        <div class="pk-text-xs pk-text-gray-400 pk-mt-0.5">{{ __('not loaded') }}</div>
      </div>
      <div class="pk-stat-card pk-bg-white pk-rounded-xl pk-p-5 pk-border pk-border-gray-100">
        <div class="pk-text-3xl pk-font-bold pk-mb-1" style="color:#f59e0b">{{ d.proWidgets }}</div>
        <div class="pk-text-sm pk-font-medium pk-text-gray-700">{{ __('Pro Widgets') }}</div>
        <div class="pk-text-xs pk-text-gray-400 pk-mt-0.5">{{ __('upgrade to unlock') }}</div>
      </div>
    </div>

    <!-- Changelog + Sidebar -->
    <div class="pk-grid pk-gap-6" style="grid-template-columns:1fr 260px">
      <!-- Changelog card -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden">
        <div class="pk-flex pk-items-center pk-justify-between pk-px-6 pk-py-4 pk-border-b pk-border-gray-100">
          <h3 class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">{{ __("What's New in v") }}{{ d.version }}</h3>
          <span class="pk-text-xs pk-font-semibold pk-px-2 pk-py-1 pk-rounded" style="background:#6c63ff;color:white">{{ __('Latest') }}</span>
        </div>
        <div class="pk-divide-y pk-divide-gray-50">
          <div v-for="(item, i) in d.changelog" :key="i"
               class="pk-flex pk-items-start pk-gap-3 pk-px-6 pk-py-3">
            <span class="pk-text-xs pk-font-semibold pk-px-2 pk-py-0.5 pk-rounded pk-flex-shrink-0 pk-mt-0.5"
                  :style="`background:${tagColor[item.type].bg};color:${tagColor[item.type].color};border:1px solid ${tagColor[item.type].border}`">
              {{ item.label }}
            </span>
            <p class="pk-text-sm pk-text-gray-600 pk-m-0 pk-leading-snug">{{ item.text }}</p>
          </div>
        </div>
      </div>

      <!-- Right sidebar -->
      <div class="pk-space-y-4">
        <!-- Performance Mode -->
        <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-p-5">
          <h4 class="pk-text-xs pk-font-semibold pk-text-gray-500 pk-uppercase pk-tracking-wider pk-mb-3 pk-m-0">{{ __('PERFORMANCE MODE') }}</h4>
          <div class="pk-space-y-2">
            <label v-for="mode in ['standard','aggressive']" :key="mode"
                   class="pk-flex pk-items-center pk-gap-3 pk-p-3 pk-rounded-lg pk-cursor-pointer pk-transition-all"
                   :style="performanceMode === mode ? 'background:rgba(108,99,255,0.08);border:1.5px solid #6c63ff' : 'background:#f9fafb;border:1.5px solid #e5e7eb'">
              <input type="radio" :value="mode" v-model="performanceMode" class="pk-hidden" />
              <div class="pk-w-3 pk-h-3 pk-rounded-full pk-flex-shrink-0"
                   :style="performanceMode === mode ? 'background:#6c63ff' : 'background:#d1d5db'"></div>
              <div>
                <div class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-capitalize">{{ mode }}</div>
                <div class="pk-text-xs pk-text-gray-400 pk-mt-0.5">
                  {{ mode === 'standard' ? __('Loads only active widget assets. Recommended for most sites.') : __('Defers all non-critical scripts. Best for performance scores.') }}
                </div>
              </div>
              <span v-if="performanceMode === mode"
                    class="pk-ml-auto pk-text-xs pk-font-medium pk-px-2 pk-py-0.5 pk-rounded" style="background:#6c63ff;color:white;flex-shrink:0">{{ __('Active') }}</span>
            </label>
          </div>
        </div>

        <!-- Widget Breakdown -->
        <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-p-5">
          <h4 class="pk-text-xs pk-font-semibold pk-text-gray-500 pk-uppercase pk-tracking-wider pk-mb-4 pk-m-0">{{ __('WIDGET BREAKDOWN') }}</h4>
          <div class="pk-space-y-3">
            <div>
              <div class="pk-flex pk-justify-between pk-text-sm pk-mb-1.5">
                <span class="pk-text-gray-700">{{ __('Free Widgets') }}</span>
                <span class="pk-font-semibold pk-text-gray-800">{{ d.freeWidgets }}</span>
              </div>
              <div class="pk-h-1.5 pk-rounded-full pk-bg-gray-100">
                <div class="pk-h-1.5 pk-rounded-full pk-transition-all" style="background:#6c63ff" :style="`width:${freePercent}%`"></div>
              </div>
            </div>
            <div>
              <div class="pk-flex pk-justify-between pk-text-sm pk-mb-1.5">
                <span class="pk-text-gray-700">{{ __('Pro Widgets') }}</span>
                <span class="pk-font-semibold pk-text-gray-800">{{ d.proWidgets }}</span>
              </div>
              <div class="pk-h-1.5 pk-rounded-full pk-bg-gray-100">
                <div class="pk-h-1.5 pk-rounded-full pk-transition-all" style="background:#f59e0b" :style="`width:${proPercent}%`"></div>
              </div>
            </div>
            <div>
              <div class="pk-flex pk-justify-between pk-text-sm pk-mb-1.5">
                <span class="pk-text-gray-700">{{ __('WooCommerce') }}</span>
                <span class="pk-font-semibold pk-text-gray-800">{{ d.wooWidgets }}</span>
              </div>
              <div class="pk-h-1.5 pk-rounded-full pk-bg-gray-100">
                <div class="pk-h-1.5 pk-rounded-full pk-transition-all" style="background:#10b981" :style="`width:${wooPercent}%`"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
