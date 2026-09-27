<script setup lang="ts">
import { nextTick, onMounted, ref, watch } from 'vue';
import { useEventListener } from '@vueuse/core';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import ProductCard from '@/components/ProductCard.vue';
import { ProductVariantListItem } from '@/types';

const props = defineProps<{
    items: ProductVariantListItem[];
}>();

const track = ref<HTMLElement | null>(null);
const canScrollPrev = ref(false);
const canScrollNext = ref(false);

function updateScrollState() {
    const el = track.value;
    if (!el) return;

    canScrollPrev.value = el.scrollLeft > 1;
    canScrollNext.value =
        el.scrollLeft + el.clientWidth < el.scrollWidth - 1;
}

function scrollByCards(direction: 1 | -1) {
    const el = track.value;
    if (!el) return;

    const card = el.querySelector<HTMLElement>('[data-slider-card]');
    const amount = card ? card.offsetWidth + 20 : el.clientWidth * 0.8;

    el.scrollBy({ left: direction * amount, behavior: 'smooth' });
}

useEventListener(track, 'scroll', updateScrollState, { passive: true });
useEventListener(window, 'resize', updateScrollState);

onMounted(updateScrollState);

watch(
    () => props.items,
    () => nextTick(updateScrollState),
);
</script>

<template>
    <div class="relative">
        <div
            ref="track"
            class="scrollbar-hide flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2"
        >
            <div
                v-for="item in items"
                :key="item.id"
                data-slider-card
                class="w-[46vw] shrink-0 snap-start sm:w-56 md:w-60 lg:w-64"
            >
                <ProductCard :item="item" />
            </div>
        </div>

        <Button
            v-if="canScrollPrev"
            type="button"
            variant="outline"
            size="icon"
            class="absolute top-[38%] -left-4 hidden size-10 -translate-y-1/2 rounded-full border-border/70 bg-background shadow-md sm:flex"
            aria-label="Previous products"
            @click="scrollByCards(-1)"
        >
            <ChevronLeft class="size-5" />
        </Button>
        <Button
            v-if="canScrollNext"
            type="button"
            variant="outline"
            size="icon"
            class="absolute top-[38%] -right-4 hidden size-10 -translate-y-1/2 rounded-full border-border/70 bg-background shadow-md sm:flex"
            aria-label="Next products"
            @click="scrollByCards(1)"
        >
            <ChevronRight class="size-5" />
        </Button>
    </div>
</template>

<style scoped>
.scrollbar-hide {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
