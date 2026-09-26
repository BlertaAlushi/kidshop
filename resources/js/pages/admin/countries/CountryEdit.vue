<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem, Country } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import FormCountry from '@/components/Forms/FormCountry.vue';

const { t } = useI18n();
const props = defineProps<{
    country: Country;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('admin.countries.edit'),
        href: route('admin.countries.edit', props.country.iso_2),
    },
];
</script>

<template>
    <Head :title="t('admin.countries.edit')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <FormCountry page_name="countries" :item="country"></FormCountry>
    </AppLayout>
</template>
