<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Category } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCategory from '@/components/Forms/FormCategory.vue';

const { t } = useI18n();
const props = defineProps<{
    category: Category;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.categories.edit'),
        href: route('admin.categories.edit', props.category.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.categories.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCategory page_name="categories" :item="category"></FormCategory>
    </AppLayout>
</template>
