<script setup lang="ts">
import FormEditCreate from '@/components/Forms/FormEditCreate.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Item } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();
const props = defineProps<{
    skin_type: Item;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.skin-types.edit'),
        href: route('admin.skin-types.edit', props.skin_type.slug),
    },
];
</script>

<template>
    <Head :title="t('admin.skin-types.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormEditCreate
            page_name="skin-types"
            :item="skin_type"
        ></FormEditCreate>
    </AppLayout>
</template>
