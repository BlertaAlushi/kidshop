<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, AdminProduct } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.products'), href: route('admin.products.index') },
];

defineProps<{
    products: AdminProduct[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    {
        accessorKey: 'category',
        header: t('admin.category'),
        cell: (info) => info.row.original.category?.name ?? '-',
    },
    {
        accessorKey: 'brand',
        header: t('home.brand'),
        cell: (info) => info.row.original.brand?.name ?? '-',
    },
    { accessorKey: 'gender', header: t('admin.gender') },
    {
        accessorKey: 'variants_count',
        header: t('admin.variants'),
    },
    {
        accessorKey: 'is_active',
        header: t('admin.is_active'),
        cell: (info) => (info.row.original.is_active ? t('admin.yes') : t('admin.no')),
    },
];
</script>

<template>
    <Head :title="t('home.products')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="products" :columns="columns" page_name="products" />
    </AppLayout>
</template>
