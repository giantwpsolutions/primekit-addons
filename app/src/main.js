import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import './style.css'
import App from './App.vue'
import Dashboard from './pages/Dashboard.vue'
import Widgets from './pages/Widgets.vue'
import Extensions from './pages/Extensions.vue'
import Integrations from './pages/Integrations.vue'
import Settings from './pages/Settings.vue'
import License from './pages/License.vue'

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    { path: '/', component: Dashboard, name: 'dashboard' },
    { path: '/widgets', component: Widgets, name: 'widgets' },
    { path: '/extensions', component: Extensions, name: 'extensions' },
    { path: '/integrations', component: Integrations, name: 'integrations' },
    { path: '/settings', component: Settings, name: 'settings' },
    { path: '/license', component: License, name: 'license' },
  ],
})

const app = createApp(App)
app.use(router)
app.mount('#primekit-app')

// Handle WP submenu links that carry a pk_route query param
router.isReady().then(() => {
  const pkRoute = new URLSearchParams(window.location.search).get('pk_route')
  if (pkRoute) router.replace('/' + pkRoute)
})
