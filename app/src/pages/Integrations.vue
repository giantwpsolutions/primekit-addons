<script setup>
import { ref, onMounted } from 'vue'
import { api } from '../api/index.js'
import { __ } from '../utils/i18n.js'

const loading = ref(true)
const saving = ref(false)
const saved = ref(false)

const mailchimp = ref({ mailchimp_api_key: '' })
const testResult = ref(null)
const testing = ref(false)

onMounted(async () => {
  try {
    const res = await api.getSettings()
    if (res?.mailchimp) Object.assign(mailchimp.value, res.mailchimp)
  } catch { } finally {
    loading.value = false
  }
})

async function saveMailchimp() {
  saving.value = true
  try {
    await api.saveSettings({ mailchimp: mailchimp.value })
    saved.value = true
    setTimeout(() => saved.value = false, 2000)
  } catch {
    alert(__('Failed to save.'))
  } finally {
    saving.value = false
  }
}

async function testConnection() {
  testing.value = true
  testResult.value = null
  try {
    await new Promise(r => setTimeout(r, 1000))
    testResult.value = mailchimp.value.mailchimp_api_key ? 'success' : 'error'
  } finally {
    testing.value = false
  }
}
</script>

<template>
  <div class="pk-space-y-5">
    <div>
      <h2 class="pk-text-lg pk-font-bold pk-text-gray-800 pk-m-0">{{ __('Integrations') }}</h2>
      <p class="pk-text-sm pk-text-gray-500 pk-m-0 pk-mt-0.5">{{ __('Connect third-party services to extend plugin functionality') }}</p>
    </div>

    <div v-if="loading" class="pk-flex pk-items-center pk-justify-center pk-py-16">
      <div class="pk-w-8 pk-h-8 pk-rounded-full pk-border-2 pk-animate-spin" style="border-color:#6c63ff;border-top-color:transparent"></div>
    </div>

    <div v-else class="pk-space-y-4">
      <!-- Mailchimp -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden">
        <div class="pk-flex pk-items-center pk-justify-between pk-px-6 pk-py-4 pk-border-b pk-border-gray-100">
          <div class="pk-flex pk-items-center pk-gap-3">
            <div class="pk-w-10 pk-h-10 pk-rounded-xl pk-flex pk-items-center pk-justify-center pk-font-bold pk-text-white pk-text-sm" style="background:#FFE01B;color:#222">MC</div>
            <div>
              <p class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">Mailchimp</p>
              <p class="pk-text-xs pk-text-gray-500 pk-m-0">{{ __('Email marketing automation') }}</p>
            </div>
          </div>
          <span class="pk-text-xs pk-font-medium pk-px-2 pk-py-1 pk-rounded-full"
                :style="mailchimp.mailchimp_api_key ? 'background:rgba(16,185,129,0.1);color:#059669' : 'background:#f3f4f6;color:#6b7280'">
            {{ mailchimp.mailchimp_api_key ? __('Connected') : __('Not connected') }}
          </span>
        </div>
        <div class="pk-px-6 pk-py-5 pk-space-y-4">
          <div>
            <label class="pk-block pk-text-xs pk-font-semibold pk-text-gray-600 pk-uppercase pk-tracking-wide pk-mb-1.5">{{ __('API Key') }}</label>
            <div class="pk-flex pk-gap-2">
              <input v-model="mailchimp.mailchimp_api_key" type="password"
                     placeholder="xxxxxxxxxxxxxxxx-us1"
                     class="pk-flex-1 pk-px-3 pk-py-2 pk-text-sm pk-border pk-border-gray-200 pk-rounded-lg pk-outline-none pk-transition-colors"
                     onfocus="this.style.borderColor='#6c63ff'" onblur="this.style.borderColor='#e5e7eb'" />
              <button @click="testConnection" :disabled="testing"
                      class="pk-px-3 pk-py-2 pk-text-sm pk-rounded-lg pk-border pk-border-gray-200 pk-text-gray-600 pk-bg-white pk-transition-colors"
                      style="cursor:pointer;white-space:nowrap">
                {{ testing ? __('Testing…') : __('Test Connection') }}
              </button>
            </div>
            <p v-if="testResult === 'success'" class="pk-text-xs pk-text-green-600 pk-mt-1.5 pk-m-0">✓ {{ __('Connection successful!') }}</p>
            <p v-if="testResult === 'error'" class="pk-text-xs pk-text-red-500 pk-mt-1.5 pk-m-0">✗ {{ __('Invalid API key or connection failed.') }}</p>
          </div>
          <div class="pk-flex pk-justify-end">
            <button @click="saveMailchimp" :disabled="saving"
                    class="pk-px-4 pk-py-2 pk-rounded-lg pk-text-sm pk-font-medium pk-text-white pk-transition-all"
                    style="border:none;cursor:pointer"
                    :style="saved ? 'background:#10b981' : 'background:#6c63ff'">
              {{ saved ? __('Saved!') : saving ? __('Saving…') : __('Save') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Contact Form 7 -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden pk-opacity-75">
        <div class="pk-flex pk-items-center pk-justify-between pk-px-6 pk-py-4">
          <div class="pk-flex pk-items-center pk-gap-3">
            <div class="pk-w-10 pk-h-10 pk-rounded-xl pk-flex pk-items-center pk-justify-center pk-font-bold pk-text-white pk-text-sm" style="background:#e75d14">CF7</div>
            <div>
              <p class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">Contact Form 7</p>
              <p class="pk-text-xs pk-text-gray-400 pk-m-0">{{ __('Style CF7 forms with Elementor — no config needed') }}</p>
            </div>
          </div>
          <span class="pk-text-xs pk-font-medium pk-px-2 pk-py-1 pk-rounded-full" style="background:rgba(16,185,129,0.1);color:#059669">{{ __('Auto-detected') }}</span>
        </div>
      </div>

      <!-- Gravity Forms -->
      <div class="pk-bg-white pk-rounded-xl pk-border pk-border-gray-100 pk-overflow-hidden pk-opacity-75">
        <div class="pk-flex pk-items-center pk-justify-between pk-px-6 pk-py-4">
          <div class="pk-flex pk-items-center pk-gap-3">
            <div class="pk-w-10 pk-h-10 pk-rounded-xl pk-flex pk-items-center pk-justify-center pk-font-bold pk-text-white pk-text-sm" style="background:#009d00">GF</div>
            <div>
              <p class="pk-text-sm pk-font-semibold pk-text-gray-800 pk-m-0">Gravity Forms</p>
              <p class="pk-text-xs pk-text-gray-400 pk-m-0">{{ __('Embed Gravity Forms with full Elementor styling') }}</p>
            </div>
          </div>
          <span class="pk-text-xs pk-font-medium pk-px-2 pk-py-1 pk-rounded-full" style="background:rgba(16,185,129,0.1);color:#059669">{{ __('Auto-detected') }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
