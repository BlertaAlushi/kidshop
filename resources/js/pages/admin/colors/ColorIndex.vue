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
    { title: t('home.colors'), href: route('admin.colors.index') },
];

defineProps<{
    colors: { id: number; name: string; hex_code: string | null }[];
}>();

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'name',
        header: t('admin.name'),
        cell: (info) =>
            h('div', { class: 'flex items-center gap-3' }, [
                h('span', {
                    class: 'size-6 shrink-0 rounded-full border shadow-xs',
                    style: { backgroundColor: info.row.original.hex_code ?? 'transparent' },
                }),
                h('span', { class: 'font-medium' }, info.getValue() as string),
            ]),
    },
    {
        accessorKey: 'hex_code',
        header: t('admin.hex_code'),
        cell: (info) =>
            h('span', { class: 'font-mono text-xs text-muted-foreground uppercase' }, (info.getValue() as string) ?? '—'),
    },
];
</script>

<template>
    <Head :title="t('home.colors')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="colors" :columns="columns" page_name="colors" :title="t('home.colors')" />
    </AppLayout>
</template>
