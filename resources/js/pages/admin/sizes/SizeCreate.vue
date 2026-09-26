<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormSize from '@/components/Forms/FormSize.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.sizes.new'), href: route('admin.sizes.create') },
];
</script>

<template>
    <Head :title="t('admin.sizes.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormSize page_name="sizes"></FormSize>
    </AppLayout>
</template>
