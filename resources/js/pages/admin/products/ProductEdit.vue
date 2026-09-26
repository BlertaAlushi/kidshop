<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, AdminProduct, Brand, Category, Color, Season, Size } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCreateEditProduct from '@/components/Forms/FormCreateEditProduct.vue';

const { t } = useI18n();

const props = defineProps<{
    product: AdminProduct;
    categories: Category[];
    brands: Brand[];
    sizes: Size[];
    colors: Color[];
    seasons: Season[];
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
        <FormCreateEditProduct
            :product="product"
            :categories="categories"
            :brands="brands"
            :sizes="sizes"
            :colors="colors"
            :seasons="seasons"
        />
    </AppLayout>
</template>
