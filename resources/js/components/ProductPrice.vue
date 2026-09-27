<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    price: number | null;
    originalPrice?: number | null;
    size?: 'sm' | 'lg';
}>();

const hasDiscount = computed(
    () =>
        props.originalPrice !== null &&
        props.originalPrice !== undefined &&
        props.price !== null &&
        props.originalPrice > props.price,
);

const discountPercent = computed(() => {
    if (!hasDiscount.value || !props.originalPrice) return 0;
    return Math.round(
        ((props.originalPrice - (props.price ?? 0)) / props.originalPrice) * 100,
    );
});
</script>

<template>
    <span class="inline-flex flex-wrap items-baseline gap-1.5">
        <span
            :class="[
                hasDiscount ? 'text-red-600' : 'text-primary',
                size === 'lg' ? 'text-2xl font-semibold' : '',
            ]"
        >
            {{ price !== null ? `${price} €` : '' }}
        </span>
        <span
            v-if="hasDiscount"
            class="text-sm text-gray-400 line-through"
        >
            {{ originalPrice }} €
        </span>
        <span
            v-if="hasDiscount"
            class="rounded-sm bg-red-600 px-1.5 py-0.5 text-xs font-semibold text-white"
        >
            -{{ discountPercent }}%
        </span>
    </span>
</template>
