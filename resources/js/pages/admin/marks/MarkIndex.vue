<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.marks'), href: route('admin.marks.index') },
];

defineProps<{
    marks: { id: number; name: string; slug: string; is_active: boolean }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'slug', header: t('admin.slug') },
    {
        accessorKey: 'is_active',
        header: t('admin.is_active'),
        cell: (info) => (info.row.original.is_active ? t('admin.yes') : t('admin.no')),
    },
];
</script>

<template>
    <Head :title="t('home.marks')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="marks" :columns="columns" page_name="marks" />
    </AppLayout>
</template>
