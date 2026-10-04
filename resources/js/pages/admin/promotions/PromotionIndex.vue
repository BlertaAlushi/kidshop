<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Promotion } from '@/types';
import { Head } from '@inertiajs/vue3';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import ActiveBadge from '@/components/ActiveBadge.vue';
import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.promotions'), href: route('admin.promotions.index') },
];

defineProps<{
    promotions: Promotion[];
}>();

const formatDate = (value: string | null) =>
    value
        ? new Date(value).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' })
        : null;

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'name',
        header: t('admin.name'),
        cell: (info) => h('span', { class: 'font-medium' }, info.getValue() as string),
    },
    {
        accessorKey: 'value',
        header: t('admin.value'),
        cell: (info) => {
            const promo = info.row.original;
            const isPercentage = promo.type === 'percentage';
            return h('div', { class: 'flex items-center gap-2' }, [
                h(
                    'span',
                    {
                        class: 'whitespace-nowrap rounded-md bg-orange-100 px-2 py-0.5 text-sm font-semibold text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                    },
                    isPercentage ? `-${Number(promo.value)}%` : `-${Number(promo.value).toFixed(2)} €`,
                ),
                h('span', { class: 'text-xs text-muted-foreground' }, isPercentage ? t('admin.percentage') : t('admin.fixed')),
            ]);
        },
    },
    {
        id: 'period',
        accessorFn: (row) => row.starts_at ?? '',
        header: t('admin.table.period'),
        enableGlobalFilter: false,
        cell: (info) => {
            const from = formatDate(info.row.original.starts_at);
            const to = formatDate(info.row.original.ends_at);
            if (!from && !to) return h('span', { class: 'text-muted-foreground' }, t('admin.table.no_limit'));
            return h('span', { class: 'whitespace-nowrap text-sm' }, `${from ?? '…'} → ${to ?? '…'}`);
        },
    },
    {
        accessorKey: 'targets_count',
        header: t('admin.targets'),
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
    <Head :title="t('home.promotions')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="promotions" :columns="columns" page_name="promotions" :title="t('home.promotions')" />
    </AppLayout>
</template>
