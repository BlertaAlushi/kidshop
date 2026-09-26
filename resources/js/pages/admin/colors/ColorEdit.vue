<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Color } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormColor from '@/components/Forms/FormColor.vue';

const { t } = useI18n();
const props = defineProps<{
    color: Color;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.colors.edit'),
        href: route('admin.colors.edit', props.color.id),
    },
];
</script>

<template>
    <Head :title="t('admin.colors.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormColor page_name="colors" :item="color"></FormColor>
    </AppLayout>
</template>
