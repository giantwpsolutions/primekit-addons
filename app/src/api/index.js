// WordPress REST API bridge
// window.primekitAdmin is localized by PHP via wp_localize_script
const base = () => window.primekitAdmin?.restUrl || '/wp-json/primekit/v1'
const nonce = () => window.primekitAdmin?.nonce || ''

async function apiFetch(path, options = {}) {
  const res = await fetch(`${base()}${path}`, {
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': nonce(),
      ...options.headers,
    },
    ...options,
  })
  if (!res.ok) throw new Error(`API error ${res.status}`)
  return res.json()
}

export const api = {
  getDashboard: () => apiFetch('/dashboard'),
  getWidgets: () => apiFetch('/widgets'),
  toggleWidget: (name, enabled) =>
    apiFetch('/widgets/toggle', {
      method: 'POST',
      body: JSON.stringify({ name, enabled }),
    }),
  getSettings: () => apiFetch('/settings'),
  saveSettings: (data) =>
    apiFetch('/settings', {
      method: 'POST',
      body: JSON.stringify(data),
    }),
  getExtensions: () => apiFetch('/extensions'),
  saveExtensions: (data) =>
    apiFetch('/extensions', {
      method: 'POST',
      body: JSON.stringify(data),
    }),
}
