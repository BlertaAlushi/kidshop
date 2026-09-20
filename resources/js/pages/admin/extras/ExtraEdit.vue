<script setup lang="ts">
import FormEditCreate from '@/components/Forms/FormEditCreate.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Item } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();
const props = defineProps<{
    extra: Item;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.extras.edit'),
        href: route('admin.extras.edit', props.extra.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.extras.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormEditCreate page_name="extras" :item="extra"></FormEditCreate>
    </AppLayout>
</template>
