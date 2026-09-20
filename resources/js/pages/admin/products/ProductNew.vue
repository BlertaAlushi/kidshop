<script setup lang="ts">
import FormCreateEditProduct from '@/components/Forms/FormCreateEditProduct.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.products.new'), href: route('admin.products.create') },
];
</script>

<template>
    <Head :title="t('admin.products.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCreateEditProduct></FormCreateEditProduct>
    </AppLayout>
</template>
