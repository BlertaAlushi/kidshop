<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, ProductForm } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCreateEditProduct from '@/components/Forms/FormCreateEditProduct.vue';

const { t } = useI18n();

const props = defineProps<{
    product: ProductForm;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.products.edit'),
        href: route('admin.products.edit', props.product.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.products.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCreateEditProduct :product="product"></FormCreateEditProduct>
    </AppLayout>
</template>
