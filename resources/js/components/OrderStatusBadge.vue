<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps<{
    status: string;
    type?: 'status' | 'payment';
}>();

const colors: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    confirmed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    ready_for_delivery: 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    paid: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
};

const isPayment = computed(() => props.type === 'payment');

const label = computed(() =>
    t(`admin.orders.${isPayment.value ? 'payment_statuses' : 'statuses'}.${props.status}`),
);

const color = computed(() =>
    isPayment.value && props.status === 'pending'
        ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
        : colors[props.status] ?? 'bg-gray-100 text-gray-700',
);
</script>

<template>
    <span
        class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="color"
    >
        {{ label }}
    </span>
</template>
