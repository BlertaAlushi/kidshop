<script setup lang="ts">
import FormCreateEditProduct from '@/components/Forms/FormCreateEditProduct.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Brand, Category, Color, Season, Size } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

defineProps<{
    categories: Category[];
    brands: Brand[];
    sizes: Size[];
    colors: Color[];
    seasons: Season[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.products.new'), href: route('admin.products.create') },
];
</script>

<template>
    <Head :title="t('admin.products.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCreateEditProduct
            :categories="categories"
            :brands="brands"
            :sizes="sizes"
            :colors="colors"
            :seasons="seasons"
        />
    </AppLayout>
</template>
