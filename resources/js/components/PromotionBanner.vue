<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import { ArrowRight } from 'lucide-vue-next';
import type { PageType } from '@/types';
import {
    formatPromotionDiscount,
    formatPromotionEndDate,
} from '@/lib/promotion';

const { t } = useI18n();
const page = usePage<PageType>();

const promotions = computed(() => page.props.activePromotions ?? []);

// Promotions arrive sorted soonest-ending first.
const message = computed(() => {
    const list = promotions.value;
    if (!list.length) return null;

    if (list.length === 1) {
        return `${list[0].name}: ${formatPromotionDiscount(list[0])}`;
    }

    const maxPercentage = Math.max(
        0,
        ...list.filter((p) => p.type === 'percentage').map((p) => p.value),
    );

    return maxPercentage
        ? t('home.sale_up_to', { discount: `${maxPercentage}%` })
        : t('home.sale_on');
});

const endsAt = computed(() => promotions.value[0]?.ends_at ?? null);
</script>

<template>
    <a
        v-if="message"
        :href="route('collection.sale')"
        class="group block w-full bg-red-600 px-4 py-2 text-center text-xs font-medium text-white transition-colors hover:bg-red-700 sm:text-sm"
    >
        <span class="inline-flex flex-wrap items-center justify-center gap-x-2 gap-y-0.5">
            <span class="font-semibold">{{ message }}</span>
            <span v-if="endsAt" class="opacity-90">
                · {{ t('home.promotion_ends', { date: formatPromotionEndDate(endsAt) }) }}
            </span>
            <span class="inline-flex items-center gap-1 underline underline-offset-2">
                {{ t('home.shop_sale') }}
                <ArrowRight class="size-3.5 transition-transform group-hover:translate-x-0.5" />
            </span>
        </span>
    </a>
</template>
