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
    { title: t('home.skin_types'), href: route('admin.skin-types.index') },
];

defineProps<{
    skin_types: { id: number; name: string; slug: string }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'slug', header: t('admin.slug') },
];
</script>

<template>
    <Head :title="t('home.body_parts')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :table_rows="skin_types"
            :columns="columns"
            page_name="skin-types"
        />
    </AppLayout>
</template>
