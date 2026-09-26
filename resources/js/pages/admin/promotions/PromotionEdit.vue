<script setup lang="ts">
import FormCreateEditPromotion from '@/components/Forms/FormCreateEditPromotion.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Brand, Category, Color, Promotion } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

const props = defineProps<{
    promotion: Promotion;
    products: { id: number; name: string }[];
    categories: Category[];
    brands: Brand[];
    colors: Color[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.promotions.edit'),
        href: route('admin.promotions.edit', props.promotion.id),
    },
];
</script>

<template>
    <Head :title="t('admin.promotions.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCreateEditPromotion
            :promotion="promotion"
            :products="products"
            :categories="categories"
            :brands="brands"
            :colors="colors"
        />
    </AppLayout>
</template>
