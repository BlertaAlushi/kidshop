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
    { title: t('home.sizes'), href: route('admin.sizes.index') },
];

defineProps<{
    sizes: { id: number; name: string; sort_order: number }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'sort_order', header: t('admin.sort_order') },
];
</script>

<template>
    <Head :title="t('home.sizes')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="sizes" :columns="columns" page_name="sizes" />
    </AppLayout>
</template>
