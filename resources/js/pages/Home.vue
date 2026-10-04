<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import { ProductVariantListItem } from '@/types';
import ProductMarquee from '@/components/ProductMarquee.vue';
import { Sparkles, Truck, RotateCcw, ShieldCheck } from 'lucide-vue-next';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '@/components/ui/empty';
import { Button } from '@/components/ui/button';
const { t } = useI18n();

const props = defineProps<{
    new_arrivals: {
        data: ProductVariantListItem[];
    };
    hero_background_image?: string | null;
    hero_title?: string | null;
    hero_description?: string | null;
}>();

const rowOne = computed(() =>
    props.new_arrivals.data.filter((_, index) => index % 2 === 0),
);
const rowTwo = computed(() => {
    const odd = props.new_arrivals.data.filter(
        (_, index) => index % 2 === 1,
    );
    return odd.length ? odd : props.new_arrivals.data;
});

const perks = [
    { icon: Truck, label: 'Free shipping over 50 €' },
    { icon: RotateCcw, label: '30-day easy returns' },
    { icon: ShieldCheck, label: 'Secure checkout' },
];
</script>

<template>
    <Head :title="t('home.home')"> </Head>

    <AppLayout>
        <div class="flex w-full flex-col overflow-hidden">
            <section class="relative isolate">
                <div
                    v-if="hero_background_image"
                    class="absolute inset-0 -z-20 bg-cover bg-center bg-no-repeat"
                    :style="{ backgroundImage: `url(${hero_background_image})` }"
                />
                <div
                    v-if="hero_background_image"
                    class="absolute inset-0 -z-20 bg-black/50"
                />
                <div
                    class="mx-auto flex w-full max-w-7xl flex-col items-center gap-6 px-6 py-20 text-center sm:py-28"
                >
                    <h1
                        class="max-w-2xl text-4xl font-semibold tracking-tight text-balance drop-shadow-md sm:text-6xl"
                        :class="hero_background_image ? 'text-white' : 'text-foreground'"
                    >
                        {{ hero_title }}
                    </h1>
                    <p
                        class="max-w-xl text-base drop-shadow-md sm:text-lg"
                        :class="hero_background_image ? 'text-white/90' : 'text-muted-foreground'"
                    >
                        {{ hero_description }}
                    </p>
                    <div class="mt-2 flex flex-wrap items-center justify-center gap-3 drop-shadow-lg">
                        <Button as-child size="lg" class="rounded-full px-8">
                            <Link :href="route('collection.all')">
                                Shop all products
                            </Link>
                        </Button>
                        <Button
                            as-child
                            size="lg"
                            variant="outline"
                            class="rounded-full px-8"
                        >
                            <Link :href="route('collection.all')">
                                Explore new arrivals
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-center gap-4 pt-10 pb-8">
                <span class="h-px w-10 bg-border sm:w-16" />
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-5 py-2 text-xs font-semibold tracking-[0.2em] text-primary uppercase shadow-sm"
                >
                    <Sparkles class="size-3.5" />
                    {{ t('home.new_arrivals') }}
                </span>
                <span class="h-px w-10 bg-border sm:w-16" />
            </div>

            <div
                v-if="new_arrivals.data.length"
                class="flex flex-col gap-6 pb-20"
            >
                <ProductMarquee :items="rowOne" direction="left" :speed="46" />
                <ProductMarquee :items="rowTwo" direction="right" :speed="52" />
            </div>
            <div v-else class="mx-auto w-full max-w-7xl px-6 pb-24">
                <Empty
                    class="rounded-2xl border border-border/70 bg-card/50 py-16"
                >
                    <EmptyHeader>
                        <EmptyTitle>{{ t('home.no_products') }}</EmptyTitle>
                        <EmptyDescription>
                            Added products will be shown here.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>
            </div>

            <section class="border-t border-border/70 bg-card/40">
                <div
                    class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-6 py-10 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div
                        v-for="perk in perks"
                        :key="perk.label"
                        class="flex items-center justify-center gap-3 sm:justify-start"
                    >
                        <component
                            :is="perk.icon"
                            class="size-5 shrink-0 text-primary"
                        />
                        <span class="text-sm font-medium text-foreground">{{
                            perk.label
                        }}</span>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
