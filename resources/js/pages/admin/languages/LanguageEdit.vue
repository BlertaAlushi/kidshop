<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Language } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormLanguage from '@/components/Forms/FormLanguage.vue';

const { t } = useI18n();
const props = defineProps<{
    language: Language;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.languages.edit'),
        href: route('admin.languages.edit', props.language.code),
    },
];
</script>

<template>
    <Head :title="t('admin.languages.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormLanguage page_name="languages" :item="language"></FormLanguage>
    </AppLayout>
</template>
