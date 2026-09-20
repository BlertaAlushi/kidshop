<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Mark } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormMark from '@/components/Forms/FormMark.vue';

const { t } = useI18n();
const props = defineProps<{
    mark: Mark;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.marks.edit'),
        href: route('admin.marks.edit', props.mark.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.marks.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormMark page_name="marks" :item="mark"></FormMark>
    </AppLayout>
</template>
