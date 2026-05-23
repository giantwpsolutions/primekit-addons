<script setup>
import { useRoute } from 'vue-router'
import { __ } from '../utils/i18n.js'

const route = useRoute()

const pageTitle = {
  '/': __('Dashboard'),
  '/widgets': __('Widgets'),
  '/extensions': __('Extensions'),
  '/integrations': __('Integrations'),
  '/settings': __('Settings'),
}
</script>

<template>
  <header class="pk-flex pk-items-center pk-justify-between pk-px-6 pk-py-3 pk-bg-white pk-border-b pk-border-gray-200 pk-flex-shrink-0" style="min-height:52px">
    <h1 class="pk-text-gray-800 pk-font-semibold pk-text-base pk-m-0">
      {{ pageTitle[route.path] || __('PrimeKit') }}
    </h1>
    <div class="pk-flex pk-items-center pk-gap-4">
      <a href="https://primekitaddons.com/docs" target="_blank"
         class="pk-text-sm pk-text-gray-500 pk-no-underline pk-flex pk-items-center pk-gap-1 hover:pk-text-gray-800 pk-transition-colors">
        {{ __('Documentation') }}
        <svg class="pk-w-3 pk-h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
          <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
      </a>
      <a href="https://primekitaddons.com/support" target="_blank"
         class="pk-text-sm pk-text-gray-500 pk-no-underline pk-flex pk-items-center pk-gap-1 hover:pk-text-gray-800 pk-transition-colors">
        {{ __('Support') }}
        <svg class="pk-w-3 pk-h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
          <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
      </a>
      <a href="#/changelog"
         class="pk-text-sm pk-text-gray-500 pk-no-underline pk-flex pk-items-center pk-gap-1 hover:pk-text-gray-800 pk-transition-colors">
        {{ __('Changelog') }}
        <svg class="pk-w-3 pk-h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </a>
    </div>
  </header>
</template>
