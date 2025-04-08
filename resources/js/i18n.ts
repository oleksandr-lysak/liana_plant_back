import { createI18n } from 'vue-i18n'

declare global {
  interface Window {
    translations?: {
      locale?: string;
      messages?: Record<string, any>;
    };
  }
}

const locale = window?.translations?.locale || 'en'
const messages = {
  [locale]: window?.translations?.messages || {},
}

const i18n = createI18n({
  legacy: false,
  locale,
  messages,
})

export default i18n
