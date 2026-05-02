<script setup>
import { RouterView, useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import TopBar from './components/TopBar.vue'
import FooterBar from './components/FooterBar.vue'

const route = useRoute()
</script>

<template>
  <div class="pk-flex" style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;min-height:calc(100vh - 32px);background:#f8f9fb;">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main area -->
    <div class="pk-flex pk-flex-col pk-flex-1" style="min-width:0">
      <TopBar />
      <main class="pk-flex-1 pk-p-6">
        <RouterView v-slot="{ Component }">
          <Transition name="pk-fade" mode="out-in">
            <component :is="Component" :key="route.path" />
          </Transition>
        </RouterView>
      </main>
      <FooterBar />
    </div>
  </div>
</template>
