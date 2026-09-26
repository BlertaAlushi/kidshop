<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormColor from '@/components/Forms/FormColor.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.colors.new'), href: route('admin.colors.create') },
];
</script>

<template>
    <Head :title="t('admin.colors.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormColor page_name="colors"></FormColor>
    </AppLayout>
</template>
