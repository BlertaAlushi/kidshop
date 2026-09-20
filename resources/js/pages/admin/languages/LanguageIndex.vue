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
    { title: t('home.languages'), href: route('admin.languages.index') },
];

defineProps<{
    languages: { id: number; language: string; code: string }[];
}>();

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'language', header: t('admin.language') },
    { accessorKey: 'code', header: t('admin.code') },
];
</script>

<template>
    <Head :title="t('home.languages')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :table_rows="languages"
            :columns="columns"
            page_name="languages"
        />
    </AppLayout>
</template>
