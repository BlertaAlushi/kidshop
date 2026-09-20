<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormLanguage from '@/components/Forms/FormLanguage.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.languages.new'), href: route('admin.languages.create') },
];
</script>

<template>
    <Head :title="t('admin.languages.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormLanguage page_name="languages"></FormLanguage>
    </AppLayout>
</template>
