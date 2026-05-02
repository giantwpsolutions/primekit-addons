<script setup>
const version = window.primekitAdmin?.version || '2.0.0'
const ratingUrl = 'https://wordpress.org/support/plugin/primekit-addons/reviews/#new-post'
</script>

<template>
  <footer class="pk-mx-6 pk-mb-6 pk-mt-2 pk-rounded-xl pk-border pk-border-gray-100 pk-bg-white pk-px-5 pk-py-3 pk-flex pk-items-center pk-justify-between pk-gap-4 pk-flex-wrap">
    <!-- Rating -->
    <div class="pk-flex pk-items-center pk-gap-2 pk-text-sm pk-text-gray-500">
      <span>Please rate</span>
      <span class="pk-font-semibold pk-text-gray-700">PrimeKit Addons</span>
      <a :href="ratingUrl" target="_blank" class="pk-flex pk-items-center pk-gap-0.5 pk-no-underline pk-transition-transform hover:pk-scale-110" title="Rate us on WordPress.org">
        <span v-for="i in 5" :key="i" style="color:#f59e0b;font-size:15px">★</span>
      </a>
      <span>on</span>
      <a :href="ratingUrl" target="_blank" class="pk-font-medium pk-no-underline pk-transition-colors" style="color:#6c63ff"
         onmouseover="this.style.color='#5a52e0'" onmouseout="this.style.color='#6c63ff'">
        WordPress.org
      </a>
      <span>to help us spread the word.</span>
    </div>

    <!-- Version badge -->
    <span class="pk-text-xs pk-font-medium pk-px-2.5 pk-py-1 pk-rounded-full pk-flex-shrink-0"
          style="background:#f3f4f6;color:#6b7280">
      v{{ version }}
    </span>
  </footer>
</template>
