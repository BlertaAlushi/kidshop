<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Product } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

import { ColumnDef } from '@tanstack/vue-table';

import DataTable from '@/components/DataTable.vue';
import { computed } from 'vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('home.products'), href: route('admin.products.index') },
];

const props = defineProps<{
    products: { data: Product[] };
}>();

const computedProducts = computed(() => props.products.data);

const columns: ColumnDef<any>[] = [
    { accessorKey: 'id', header: 'ID' },
    { accessorKey: 'name', header: t('admin.name') },
    { accessorKey: 'slug', header: t('admin.slug') },
    { accessorKey: 'price', header: t('admin.price') },
    { accessorKey: 'currency', header: t('admin.currency') },
    { accessorKey: 'stock_quantity', header: t('admin.stock_quantity') },
    { accessorKey: 'mark', header: t('home.brand') },
];
</script>

<template>
    <Head :title="t('home.products')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :table_rows="computedProducts"
            :columns="columns"
            page_name="products"
        />
    </AppLayout>
</template>
