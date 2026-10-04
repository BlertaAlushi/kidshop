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
    { title: t('home.sizes'), href: route('admin.sizes.index') },
];

defineProps<{
    sizes: { id: number; name: string; sort_order: number }[];
}>();

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'name',
        header: t('admin.name'),
        cell: (info) =>
            h(
                'span',
                { class: 'inline-flex min-w-10 justify-center rounded-md border bg-muted/50 px-2 py-0.5 font-medium' },
                info.getValue() as string,
            ),
    },
    {
        accessorKey: 'sort_order',
        header: t('admin.sort_order'),
        cell: (info) => h('span', { class: 'text-muted-foreground tabular-nums' }, String(info.getValue())),
    },
];
</script>

<template>
    <Head :title="t('home.sizes')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :table_rows="sizes" :columns="columns" page_name="sizes" :title="t('home.sizes')" />
    </AppLayout>
</template>
