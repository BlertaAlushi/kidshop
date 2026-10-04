<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { h } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import ActiveBadge from '@/components/ActiveBadge.vue';
import DataTable from '@/components/DataTable.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.categories'), href: route('admin.categories.index') },
];

defineProps<{
    categories: { id: number; name: string; slug: string; is_active: boolean }[];
}>();

const columns: ColumnDef<any>[] = [
    {
        accessorKey: 'name',
        header: t('admin.name'),
        cell: (info) => h('span', { class: 'font-medium' }, info.getValue() as string),
    },
    {
        accessorKey: 'slug',
        header: t('admin.slug'),
        cell: (info) => h('span', { class: 'text-muted-foreground' }, info.getValue() as string),
    },
    {
        accessorKey: 'is_active',
        header: t('admin.is_active'),
        cell: (info) => h(ActiveBadge, { active: !!info.getValue() }),
    },
];
</script>

<template>
    <Head :title="t('home.categories')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :table_rows="categories"
            :columns="columns"
            page_name="categories"
            :title="t('home.categories')"
        />
    </AppLayout>
</template>
