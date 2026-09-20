import { createI18n } from 'vue-i18n';

export const i18n = createI18n({
    legacy: false,
    fallbackLocale: 'en',
    globalInjection: true,
    messages: {},
});

const localeModules = import.meta.glob('./locales/*/*.json');

interface LocaleModule {
    default: Record<string, string>;
}

export async function loadLocale(locale: string) {
    const messages: Record<string, Record<string, string>> = {};

    for (const path in localeModules) {



        if (path.includes(`/${locale}/`)) {
            const mod = (await localeModules[path]()) as LocaleModule;
            const namespace = path.split('/').pop()?.replace('.json', '');
            if (namespace) messages[namespace] = mod.default;
        }
    }

    i18n.global.setLocaleMessage(locale, messages);
    i18n.global.locale.value = locale;
}
