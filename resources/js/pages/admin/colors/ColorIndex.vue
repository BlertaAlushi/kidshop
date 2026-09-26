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
    { title: t('home.colors'), href: route('admin.colors.index') },
];

defineProps<{
    colors: { id: number; name: string; hex_code: string | null }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'hex_code', header: t('admin.hex_code') },
];
</script>

<template>
    <Head :title="t('home.colors')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="colors" :columns="columns" page_name="colors" />
    </AppLayout>
</template>
