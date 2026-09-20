<script setup lang="ts">
import FormEditCreate from '@/components/Forms/FormEditCreate.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Item } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();
const props = defineProps<{
    product_type: Item;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.product-types.edit'),
        href: route('admin.product-types.edit', props.product_type.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.product-types.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormEditCreate
            page_name="product-types"
            :item="product_type"
        ></FormEditCreate>
    </AppLayout>
</template>
