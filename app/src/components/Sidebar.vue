<script setup>
import { RouterLink, useRoute } from 'vue-router'
import logo from '../assets/primekit-logo.svg'
import { widgetTotal } from '../store/widgets.js'
import { __ } from '../utils/i18n.js'

const route = useRoute()
const version = window.primekitAdmin?.version || '1.0.0'

const isPro = window.primekitAdmin?.isPro || false

const navItems = [
  { to: '/', label: __('Dashboard'), icon: 'grid' },
  { to: '/widgets', label: __('Widgets'), icon: 'puzzle', badge: widgetTotal },
  { to: '/extensions', label: __('Extensions'), icon: 'layers' },
  { to: '/integrations', label: __('Integrations'), icon: 'share2' },
  { to: '/settings', label: __('Settings'), icon: 'settings' },
  ...(isPro ? [{ to: '/license', label: __('License'), icon: 'license' }] : []),
]

const isActive = (path) => {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}
</script>

<template>
  <aside class="pk-flex-shrink-0 pk-flex pk-flex-col pk-sidebar-scroll"
         style="width:360px;background:#ffffff;border-right:1px solid #e5e7eb;overflow-y:auto;min-height:calc(100vh - 32px);position:sticky;top:32px;align-self:flex-start;">

    <!-- Logo -->
    <div class="pk-flex pk-items-center pk-gap-3 pk-px-5 pk-py-5" style="border-bottom:1px solid #f3f4f6;">
      <img :src="logo" alt="PrimeKit" class="pk-w-9 pk-h-9 pk-flex-shrink-0" style="object-fit:contain;" />
      <div>
        <div class="pk-font-bold pk-text-sm pk-leading-tight" style="color:#111827;">PrimeKit</div>
        <div class="pk-text-xs pk-mt-0.5" style="color:#9ca3af;">{{ __('Addons') }} v{{ version }}</div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="pk-flex-1 pk-px-3 pk-py-3 pk-space-y-0.5">
      <RouterLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="pk-flex pk-items-center pk-gap-3 pk-px-3 pk-py-2.5 pk-rounded-lg pk-text-sm pk-transition-all pk-duration-150 pk-no-underline"
        :style="isActive(item.to)
          ? 'background:#ede9fe;color:#6c63ff;font-weight:600;'
          : 'color:#6b7280;font-weight:500;'"
        @mouseenter="e => { if(!isActive(item.to)) e.currentTarget.style.background='#f9fafb'; }"
        @mouseleave="e => { if(!isActive(item.to)) e.currentTarget.style.background=''; }"
      >
        <svg class="pk-w-4 pk-h-4 pk-flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <template v-if="item.icon==='grid'">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
          </template>
          <template v-else-if="item.icon==='puzzle'">
            <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            <path d="M9 12h6M12 9v6"/>
          </template>
          <template v-else-if="item.icon==='layers'">
            <polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/>
            <polyline points="2 12 12 17 22 12"/>
          </template>
          <template v-else-if="item.icon==='share2'">
            <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
          </template>
          <template v-else-if="item.icon==='settings'">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
          </template>
          <template v-else-if="item.icon==='license'">
            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
          </template>
        </svg>

        <span class="pk-flex-1">{{ item.label }}</span>

        <span v-if="item.badge"
              class="pk-text-xs pk-px-2 pk-py-0.5 pk-rounded-full pk-font-semibold"
              :style="isActive(item.to)
                ? 'background:#6c63ff;color:white;'
                : 'background:#f3f4f6;color:#6b7280;'">
          {{ item.badge.value !== undefined ? item.badge.value : item.badge }}
        </span>
      </RouterLink>
    </nav>

    <!-- Bottom actions -->
    <div class="pk-px-3 pk-pb-4 pk-space-y-1.5" style="border-top:1px solid #f3f4f6;padding-top:12px;margin-top:8px;">
      <a href="#"
         class="pk-flex pk-items-center pk-gap-2.5 pk-px-3 pk-py-2.5 pk-rounded-lg pk-text-sm pk-font-semibold pk-no-underline pk-transition-all"
         style="color:#d97706;background:#fffbeb;border:1px solid #fde68a;"
         @mouseenter="e=>e.currentTarget.style.background='#fef3c7'"
         @mouseleave="e=>e.currentTarget.style.background='#fffbeb'">
        <svg class="pk-w-4 pk-h-4" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        {{ __('Upgrade to Pro') }}
      </a>

      <a href="#"
         class="pk-flex pk-items-center pk-gap-2.5 pk-px-3 pk-py-2.5 pk-rounded-lg pk-text-sm pk-font-medium pk-no-underline pk-transition-all"
         style="color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;"
         @mouseenter="e=>{ e.currentTarget.style.background='#f3f4f6'; e.currentTarget.style.color='#374151'; }"
         @mouseleave="e=>{ e.currentTarget.style.background='#f9fafb'; e.currentTarget.style.color='#6b7280'; }">
        <svg class="pk-w-4 pk-h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/>
        </svg>
        {{ __('Setup Wizard') }}
      </a>
    </div>

  </aside>
</template>
