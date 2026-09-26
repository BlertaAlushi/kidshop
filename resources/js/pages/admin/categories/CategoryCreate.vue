<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCategory from '@/components/Forms/FormCategory.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.categories.new'), href: route('admin.categories.create') },
];
</script>

<template>
    <Head :title="t('admin.categories.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCategory page_name="categories"></FormCategory>
    </AppLayout>
</template>
