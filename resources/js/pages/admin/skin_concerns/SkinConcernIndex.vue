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
    {
        title: t('home.skin_concerns'),
        href: route('admin.skin-concerns.index'),
    },
];

defineProps<{
    skin_concerns: { id: number; name: string; slug: string }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'slug', header: t('admin.slug') },
];
</script>

<template>
    <Head :title="t('home.skin_concerns')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :table_rows="skin_concerns"
            :columns="columns"
            page_name="skin-concerns"
        />
    </AppLayout>
</template>
