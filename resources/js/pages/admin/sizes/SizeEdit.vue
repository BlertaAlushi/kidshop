<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Size } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormSize from '@/components/Forms/FormSize.vue';

const { t } = useI18n();
const props = defineProps<{
    size: Size;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.sizes.edit'),
        href: route('admin.sizes.edit', props.size.id),
    },
];
</script>

<template>
    <Head :title="t('admin.sizes.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormSize page_name="sizes" :item="size"></FormSize>
    </AppLayout>
</template>
