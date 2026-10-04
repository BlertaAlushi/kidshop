<script setup lang="ts">
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps<{
    active: boolean;
}>();
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="
            active
                ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
        "
    >
        <span class="size-1.5 rounded-full" :class="active ? 'bg-green-500' : 'bg-gray-400'" />
        {{ active ? t('admin.table.active') : t('admin.table.inactive') }}
    </span>
</template>
