<script setup lang="ts">
import { computed } from 'vue';
import ProductCard from '@/components/ProductCard.vue';
import { ProductVariantListItem } from '@/types';

const props = defineProps<{
    items: ProductVariantListItem[];
    direction?: 'left' | 'right';
    speed?: number;
}>();

const loop = computed(() => [...props.items, ...props.items]);
</script>

<template>
    <div
        class="relative overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_6%,black_94%,transparent)]"
    >
        <div
            class="marquee-track flex w-max gap-5 py-2"
            :class="direction === 'right' ? 'marquee-reverse' : ''"
            :style="{ animationDuration: `${speed ?? 42}s` }"
        >
            <div
                v-for="(item, index) in loop"
                :key="`${item.id}-${index}`"
                class="w-[46vw] shrink-0 sm:w-56 md:w-60 lg:w-64"
            >
                <ProductCard :item="item" />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes marquee {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}

.marquee-track {
    animation: marquee linear infinite;
}

.marquee-track.marquee-reverse {
    animation-direction: reverse;
}

.marquee-track:hover {
    animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
    .marquee-track {
        animation: none;
    }
}
</style>
