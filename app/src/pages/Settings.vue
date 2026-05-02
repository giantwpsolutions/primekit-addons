<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../api/index.js'

const loading = ref(true)
const saving = ref(false)
const saved = ref(false)
const toast = ref(null)

const performanceMode = ref('standard')
const sysInfo = ref({ plugin: '…', wp: '…', php: '…', elementor: '…' })

const settings = ref({
  cost_estimation_package_1: 'Low',
  cost_estimation_package_2: 'Medium',
  cost_estimation_package_3: 'High',
})

onMounted(async () => {
  try {
    const res = await api.getSettings()
    if (res?.cost_estimation) Object.assign(settings.value, res.cost_estimation)
    if (res?.performanceMode) performanceMode.value = res.performanceMode
    if (res?.sysInfo) Object.assign(sysInfo.value, res.sysInfo)
  } catch { } finally {
    loading.value = false
  }
})

let toastTimer = null
function showToast(msg, type = 'success') {
  toast.value = { msg, type }
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => toast.value = null, 2500)
}

async function savePerformanceMode() {
  try {
    await api.saveSettings({ performanceMode: performanceMode.value })
    showToast(`Performance mode set to "${performanceMode.value}"`)
  } catch {
    showToast('Failed to save. Please try again.', 'error')
  }
}

async function save() {
  saving.value = true
  try {
    await api.saveSettings({
      cost_estimation: settings.value,
      performanceMode: performanceMode.value,
    })
    saved.value = true
    showToast('Settings saved successfully!')
    setTimeout(() => saved.value = false, 2000)
  } catch {
    showToast('Failed to save settings.', 'error')
  } finally {
    saving.value = false
  }
}

const modes = [
  {
    key: 'standard',
    title: 'Standard',
    desc: 'Loads only active widget assets. Recommended for most sites.',
  },
  {
    key: 'aggressive',
    title: 'Aggressive',
    desc: 'Defers all non-critical scripts. Best for performance scores.',
  },
]
</script>

<template>
  <div class="pk-space-y-5">
    <div class="pk-flex pk-items-center pk-justify-between">
      <div>
        <h2 class="pk-text-lg pk-font-bold pk-text-gray-800 pk-m-0">Settings</h2>
        <p class="pk-text-sm pk-text-gray-500 pk-m-0 pk-mt-0.5">General plugin configuration</p>
      </div>
      <button @click="save" :disabled="saving"
              class="pk-flex pk-items-center pk-gap-2 pk-px-4 pk-py-2 pk-rounded-lg pk-text-sm pk-font-medium pk-text-white pk-transition-all"
              style="border:none;cursor:pointer"
              :style="saved ? 'background:#10b981' : 'background:#6c63ff'">
        <svg v-if="saved" class="pk-w-4 pk-h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ saved ? 'Saved!' : saving ? 'Saving…' : 'Save Changes' }}
      </button>
    </div>

    <div v-if="loading" class="pk-flex pk-items-center pk-justify-center pk-py-16">
      <div class="pk-w-8 pk-h-8 pk-rounded-full pk-border-2 pk-animate-spin" style="border-color:#6c63ff;border-top-color:transparent"></div>
    </div>

    <div v-else class="pk-space-y-4">

      <!-- Performance Mode -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden">
        <div class="pk-px-6 pk-py-4 pk-border-b pk-border-gray-100">
          <h3 class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">Performance Mode</h3>
          <p class="pk-text-xs pk-text-gray-500 pk-m-0 pk-mt-0.5">Control how widget assets are loaded on the frontend</p>
        </div>
        <div class="pk-px-6 pk-py-5 pk-space-y-3">
          <label v-for="mode in modes" :key="mode.key"
                 style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border-radius:10px;border:2px solid;cursor:pointer;transition:border-color 0.15s,background 0.15s;"
                 :style="performanceMode === mode.key
                   ? 'border-color:#6c63ff;background:rgba(108,99,255,0.04)'
                   : 'border-color:#e5e7eb;background:white'">
            <input type="radio" :value="mode.key" v-model="performanceMode" @change="savePerformanceMode" style="margin-top:3px;accent-color:#6c63ff;flex-shrink:0;" />
            <div style="flex:1">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:14px;font-weight:600;color:#1f2937;">{{ mode.title }}</span>
                <span v-if="performanceMode === mode.key"
                      style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;background:#6c63ff;color:white;letter-spacing:0.3px;">
                  Active
                </span>
              </div>
              <p style="margin:4px 0 0;font-size:12px;color:#6b7280;line-height:1.5;">{{ mode.desc }}</p>
            </div>
          </label>
        </div>
      </div>

      <!-- Cost Estimation -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden">
        <div class="pk-px-6 pk-py-4 pk-border-b pk-border-gray-100">
          <h3 class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">Cost Estimation Widget</h3>
          <p class="pk-text-xs pk-text-gray-500 pk-m-0 pk-mt-0.5">Configure package labels for the cost estimator</p>
        </div>
        <div class="pk-px-6 pk-py-5 pk-space-y-4">
          <div v-for="(n, i) in [1,2,3]" :key="n" class="pk-flex pk-items-center pk-gap-4">
            <label class="pk-text-sm pk-font-medium pk-text-gray-700 pk-w-28 pk-flex-shrink-0">Package {{ n }}</label>
            <input v-model="settings[`cost_estimation_package_${n}`]" type="text"
                   :placeholder="`e.g. ${['Low','Medium','High'][i]}`"
                   class="pk-flex-1 pk-px-3 pk-py-2 pk-text-sm pk-border pk-border-gray-200 pk-rounded-lg pk-outline-none pk-max-w-xs"
                   onfocus="this.style.borderColor='#6c63ff'" onblur="this.style.borderColor='#e5e7eb'" />
          </div>
        </div>
      </div>

      <!-- About -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-p-6">
        <h3 class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0 pk-mb-3">About PrimeKit</h3>
        <div class="pk-grid pk-grid-cols-2 pk-gap-3">
          <div v-for="item in [
            { label: 'Plugin Version',   value: sysInfo.plugin },
            { label: 'Elementor Version', value: sysInfo.elementor },
            { label: 'WordPress Version', value: sysInfo.wp },
            { label: 'PHP Version',       value: sysInfo.php },
          ]" :key="item.label"
               class="pk-flex pk-items-center pk-justify-between pk-p-3 pk-rounded-lg pk-bg-gray-50">
            <span class="pk-text-xs pk-text-gray-500">{{ item.label }}</span>
            <span class="pk-text-xs pk-font-semibold pk-text-gray-700">{{ item.value }}</span>
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
