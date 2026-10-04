<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { h } from 'vue';
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

// Turns an ISO 3166-1 alpha-2 code into its flag emoji
const flag = (iso: string) =>
    iso?.length === 2
        ? String.fromCodePoint(...[...iso.toUpperCase()].map((c) => 0x1f1a5 + c.charCodeAt(0)))
        : '';

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'country',
        header: t('admin.name'),
        cell: (info) =>
            h('div', { class: 'flex items-center gap-3' }, [
                h('span', { class: 'text-xl leading-none' }, flag(info.row.original.iso_2)),
                h('span', { class: 'font-medium' }, info.getValue() as string),
            ]),
    },
    {
        accessorKey: 'iso_2',
        header: t('admin.iso_2'),
        cell: (info) =>
            h('span', { class: 'rounded bg-muted px-1.5 py-0.5 font-mono text-xs' }, info.getValue() as string),
    },
    {
        accessorKey: 'delivery_fee',
        header: t('admin.delivery_fee'),
        cell: (info) => h('span', { class: 'tabular-nums' }, `${Number(info.getValue()).toFixed(2)} €`),
    },
];
</script>

<template>
    <Head :title="t('home.countries')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="countries" :columns="columns" page_name="countries" :title="t('home.countries')" />
    </AppLayout>
</template>
