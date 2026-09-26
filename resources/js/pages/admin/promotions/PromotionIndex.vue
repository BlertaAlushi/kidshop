<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Promotion } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.promotions'), href: route('admin.promotions.index') },
];

defineProps<{
    promotions: Promotion[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    {
        accessorKey: 'type',
        header: t('admin.type'),
        cell: (info) =>
            info.row.original.type === 'percentage' ? t('admin.percentage') : t('admin.fixed'),
    },
    {
        accessorKey: 'value',
        header: t('admin.value'),
        cell: (info) =>
            info.row.original.type === 'percentage'
                ? `${info.row.original.value}%`
                : info.row.original.value,
    },
    {
        accessorKey: 'targets_count',
        header: t('admin.targets'),
    },
    {
        accessorKey: 'is_active',
        header: t('admin.is_active'),
        cell: (info) => (info.row.original.is_active ? t('admin.yes') : t('admin.no')),
    },
];
</script>

<template>
    <Head :title="t('home.promotions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="promotions" :columns="columns" page_name="promotions" />
    </AppLayout>
</template>
