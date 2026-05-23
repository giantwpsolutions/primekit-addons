const domain = 'primekit-addons'

export function __(text) {
  return window.wp?.i18n?.__?.(text, domain) ?? text
}

export function _n(single, plural, number) {
  return window.wp?.i18n?._n?.(single, plural, number, domain) ?? (number === 1 ? single : plural)
}
