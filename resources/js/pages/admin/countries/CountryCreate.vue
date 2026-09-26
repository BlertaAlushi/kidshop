<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCountry from '@/components/Forms/FormCountry.vue';

const { t } = useI18n();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.countries.new'), href: route('admin.countries.create') },
];
</script>

<template>
    <Head :title="t('admin.countries.new')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCountry page_name="countries"></FormCountry>
    </AppLayout>
</template>
