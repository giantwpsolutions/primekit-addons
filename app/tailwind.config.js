/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        sidebar: '#0f1117',
        'sidebar-hover': '#1a1d2e',
        primary: '#6c63ff',
        'primary-dark': '#5a52e0',
      },
    },
  },
  plugins: [],
  prefix: 'pk-',
  corePlugins: {
    preflight: false,
  },
}
