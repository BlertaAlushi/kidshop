<script setup lang="ts">
import FormCreateEditPromotion from '@/components/Forms/FormCreateEditPromotion.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Brand, Category, Color } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

defineProps<{
    products: { id: number; name: string }[];
    categories: Category[];
    brands: Brand[];
    colors: Color[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.promotions.new'), href: route('admin.promotions.create') },
];
</script>

<template>
    <Head :title="t('admin.promotions.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCreateEditPromotion
            :products="products"
            :categories="categories"
            :brands="brands"
            :colors="colors"
        />
    </AppLayout>
</template>
