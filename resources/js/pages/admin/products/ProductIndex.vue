<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, AdminProduct } from '@/types';
import { Head } from '@inertiajs/vue3';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import ActiveBadge from '@/components/ActiveBadge.vue';
import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.products'), href: route('admin.products.index') },
];

defineProps<{
    products: AdminProduct[];
}>();

const genderColors: Record<string, string> = {
    boy: 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
    girl: 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-300',
    unisex: 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
};

const textOrDash = (value: string | undefined) =>
    h('span', { class: value ? '' : 'text-muted-foreground' }, value ?? '—');

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'name',
        header: t('admin.name'),
        cell: (info) =>
            h('div', [
                h('div', { class: 'font-medium' }, info.getValue() as string),
                h('div', { class: 'text-xs text-muted-foreground' }, info.row.original.slug),
            ]),
    },
    {
        id: 'category',
        accessorFn: (row) => row.category?.name,
        header: t('admin.category'),
        cell: (info) => textOrDash(info.getValue() as string | undefined),
    },
    {
        id: 'brand',
        accessorFn: (row) => row.brand?.name,
        header: t('home.brand'),
        cell: (info) => textOrDash(info.getValue() as string | undefined),
    },
    {
        accessorKey: 'gender',
        header: t('admin.gender'),
        cell: (info) => {
            const gender = info.getValue() as string;
            return h(
                'span',
                { class: ['whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium', genderColors[gender]] },
                t('admin.' + gender),
            );
        },
    },
    {
        accessorKey: 'variants_count',
        header: t('admin.variants'),
        cell: (info) => h('span', { class: 'tabular-nums' }, String(info.getValue() ?? 0)),
    },
    {
        accessorKey: 'is_active',
        header: t('admin.is_active'),
        cell: (info) => h(ActiveBadge, { active: !!info.getValue() }),
    },
];
</script>

<template>
    <Head :title="t('home.products')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="products" :columns="columns" page_name="products" :title="t('home.products')" />
    </AppLayout>
</template>
