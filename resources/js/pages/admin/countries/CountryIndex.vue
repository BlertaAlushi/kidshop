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
    { title: t('home.countries'), href: route('admin.countries.index') },
];

defineProps<{
    countries: { iso_2: string; country: string; delivery_fee: number }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'iso_2', header: t('admin.iso_2') },
    { accessorKey: 'country', header: t('admin.name') },
    { accessorKey: 'delivery_fee', header: t('admin.delivery_fee') },
];
</script>

<template>
    <Head :title="t('home.countries')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="countries" :columns="columns" page_name="countries" />
    </AppLayout>
</template>
