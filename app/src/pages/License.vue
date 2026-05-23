<script setup>
import { ref, onMounted } from 'vue'
import { __ } from '../utils/i18n.js'

const ajaxUrl   = window.primekitAdmin?.ajaxUrl || ''
const nonce     = window.primekitAdmin?.licenseNonce || ''

const status    = ref('loading')
const maskedKey = ref('')
const inputKey  = ref('')
const message   = ref('')
const msgType   = ref('')
const busy      = ref(false)

const statusLabels = {
  valid:              { text: __('Active'),        color: '#16a34a', bg: '#f0fdf4', border: '#bbf7d0' },
  expired:            { text: __('Expired'),       color: '#d97706', bg: '#fffbeb', border: '#fde68a' },
  revoked:            { text: __('Revoked'),       color: '#dc2626', bg: '#fef2f2', border: '#fecaca' },
  inactive:           { text: __('Inactive'),      color: '#dc2626', bg: '#fef2f2', border: '#fecaca' },
  activation_limit:   { text: __('Limit Reached'), color: '#dc2626', bg: '#fef2f2', border: '#fecaca' },
  unregistered:       { text: __('Not Active'),    color: '#6b7280', bg: '#f9fafb', border: '#e5e7eb' },
  loading:            { text: __('Loading…'),      color: '#6b7280', bg: '#f9fafb', border: '#e5e7eb' },
}

function statusInfo() {
  return statusLabels[status.value] || statusLabels.unregistered
}

async function doAjax(action, extra = {}) {
  const body = new URLSearchParams({ action, nonce, ...extra })
  const res  = await fetch(ajaxUrl, { method: 'POST', body })
  return res.json()
}

async function loadStatus() {
  status.value = 'loading'
  const r = await doAjax('primekitpro_get_license')
  if (r.success) {
    status.value    = r.data.status
    maskedKey.value = r.data.maskedKey
  }
}

async function activate() {
  if (!inputKey.value.trim()) {
    message.value = __('Please enter a license key.')
    msgType.value = 'error'
    return
  }
  busy.value = true
  message.value = ''
  const r = await doAjax('primekitpro_activate_license', { licenseKey: inputKey.value.trim() })
  busy.value = false
  if (r.success) {
    status.value    = 'valid'
    maskedKey.value = r.data.maskedKey || maskedKey.value
    inputKey.value  = ''
    message.value   = r.data.message
    msgType.value   = 'success'
  } else {
    message.value = r.data?.message || __('Activation failed.')
    msgType.value = 'error'
  }
}

async function deactivate() {
  busy.value    = true
  message.value = ''
  const r = await doAjax('primekitpro_deactivate_license')
  busy.value = false
  if (r.success) {
    status.value    = 'unregistered'
    maskedKey.value = ''
    message.value   = r.data.message
    msgType.value   = 'success'
  } else {
    message.value = r.data?.message || __('Deactivation failed.')
    msgType.value = 'error'
  }
}

onMounted(loadStatus)
</script>

<template>
  <div style="max-width:680px;">

    <!-- Header -->
    <div style="margin-bottom:24px;">
      <h1 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 4px;">{{ __('License') }}</h1>
      <p style="color:#6b7280;font-size:14px;margin:0;">{{ __('Manage your PrimeKit Pro license key.') }}</p>
    </div>

    <!-- Status Card -->
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
          <div style="font-size:13px;color:#9ca3af;font-weight:500;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">{{ __('License Status') }}</div>
          <span :style="`display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:999px;font-size:13px;font-weight:600;color:${statusInfo().color};background:${statusInfo().bg};border:1px solid ${statusInfo().border};`">
            <span v-if="status==='valid'" style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;"></span>
            {{ statusInfo().text }}
          </span>
        </div>
        <div v-if="maskedKey" style="font-size:13px;color:#6b7280;">
          {{ __('Key:') }} <span style="font-family:monospace;color:#374151;font-weight:600;">{{ maskedKey }}</span>
        </div>
      </div>
    </div>

    <!-- Activate Form -->
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
      <h2 style="font-size:15px;font-weight:600;color:#111827;margin:0 0 16px;">
        {{ status === 'valid' ? __('Update License Key') : __('Activate License') }}
      </h2>

      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <input
          v-model="inputKey"
          type="text"
          :placeholder="__('Enter your license key')"
          style="flex:1;min-width:220px;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;color:#111827;outline:none;transition:border .15s;"
          @focus="e=>e.target.style.borderColor='#6c63ff'"
          @blur="e=>e.target.style.borderColor='#d1d5db'"
        />
        <button
          @click="activate"
          :disabled="busy"
          style="padding:10px 20px;background:#6c63ff;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;opacity:1;transition:opacity .15s;"
          :style="busy ? 'opacity:.6;cursor:not-allowed;' : ''"
        >
          {{ busy ? __('Processing…') : __('Activate') }}
        </button>
      </div>

      <p style="font-size:12px;color:#9ca3af;margin:10px 0 0;">
        {{ __('You can find your license key in your') }}
        <a href="https://www.giantwpsolutions.com/account/" target="_blank" style="color:#6c63ff;text-decoration:none;">Giant WP Solutions</a>.
      </p>
    </div>

    <!-- Deactivate -->
    <div v-if="status === 'valid'" style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px;">
      <h2 style="font-size:15px;font-weight:600;color:#111827;margin:0 0 8px;">{{ __('Deactivate License') }}</h2>
      <p style="font-size:13px;color:#6b7280;margin:0 0 16px;">{{ __('Deactivating will remove this site from your license activations.') }}</p>
      <button
        @click="deactivate"
        :disabled="busy"
        style="padding:9px 18px;background:#fff;color:#dc2626;border:1px solid #fecaca;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s;"
        :style="busy ? 'opacity:.6;cursor:not-allowed;' : ''"
        @mouseenter="e=>{ if(!busy) e.target.style.background='#fef2f2' }"
        @mouseleave="e=>e.target.style.background='#fff'"
      >
        {{ busy ? __('Processing…') : __('Deactivate License') }}
      </button>
    </div>

    <!-- Message -->
    <div v-if="message"
         :style="`padding:12px 16px;border-radius:8px;font-size:14px;font-weight:500;
           ${msgType==='success'
             ? 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;'
             : 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca;'}`">
      {{ message }}
    </div>

  </div>
</template>
