<script setup lang="ts">
import FormEditCreate from '@/components/Forms/FormEditCreate.vue';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.skin-types.new'),
        href: route('admin.skin-types.create'),
    },
];
</script>

<template>
    <Head :title="t('admin.skin-types.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormEditCreate page_name="skin-types"></FormEditCreate>
    </AppLayout>
</template>
