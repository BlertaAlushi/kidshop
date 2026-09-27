<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { ProductVariantListItem } from '@/types';
import ProductPrice from '@/components/ProductPrice.vue';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemHeader,
    ItemTitle,
} from '@/components/ui/item';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '@/components/ui/empty';
const { t } = useI18n();

defineProps<{
    new_arrivals: {
        data: ProductVariantListItem[];
    };
}>();
</script>

<template>
    <Head :title="t('home.home')"> </Head>

    <AppLayout>
        <div
            v-if="new_arrivals.data.length"
            class="flex min-h-screen flex-col gap-y-10 bg-slate-50 p-10"
        >
            <div class="space-y-2 text-center">
                <p class="text-2xl font-medium tracking-tight">
                    {{ t('home.new_arrivals') }}
                </p>
            </div>
            <div>
                <ItemGroup class="grid grid-cols-4 gap-10">
                    <Item
                        class="bg-white shadow-sm transition hover:shadow-md"
                        v-for="item in new_arrivals.data"
                        :key="item.id"
                        variant="outline"
                        as-child
                        role="listitem"
                    >
                        <a
                            :href="
                                route('collection.product', item.slug) +
                                (item.color ? `?color=${item.color.id}` : '')
                            "
                        >
                            <ItemHeader class="flex-col items-start justify-start">
                                <img
                                    :src="item.image"
                                    :alt="item.name"
                                    width="128"
                                    height="128"
                                    class="aspect-square w-full rounded-sm object-cover"
                                />
                                <div
                                    v-if="item.colors?.length"
                                    class="mt-2 flex flex-wrap items-center gap-1.5"
                                >
                                    <span
                                        v-for="color in item.colors"
                                        :key="color.id"
                                        :title="color.name"
                                        role="button"
                                        tabindex="0"
                                        class="size-6 shrink-0 cursor-pointer rounded-sm border-2 transition"
                                        :class="
                                            item.color?.id === color.id
                                                ? 'border-primary'
                                                : 'border-black/10'
                                        "
                                        :style="{
                                            backgroundColor:
                                                color.hex_code ?? '#e5e5e5',
                                        }"
                                        @click.stop.prevent="
                                            router.get(
                                                route(
                                                    'collection.product',
                                                    item.slug,
                                                ) + `?color=${color.id}`,
                                            )
                                        "
                                    />
                                </div>
                            </ItemHeader>
                            <ItemContent>
                                <ItemTitle>{{ item.name }}</ItemTitle>
                                <ItemDescription>
                                    <ProductPrice
                                        :price="item.price"
                                        :original-price="item.original_price"
                                    />
                                </ItemDescription>
                            </ItemContent>
                        </a>
                    </Item>
                </ItemGroup>
            </div>
            <Link
                :href="route('collection.all')"
                class="font-medium tracking-tight text-center"
            >
                Shop all products
            </Link>
        </div>
        <div v-else>
            <Empty
                class="h-full bg-linear-to-b from-muted/50 from-30% to-background"
            >
                <EmptyHeader>
                    <EmptyTitle>{{ t('home.no_products') }}</EmptyTitle>
                    <EmptyDescription>
                        Added products will be shown here.
                    </EmptyDescription>
                </EmptyHeader>
            </Empty>
        </div>
    </AppLayout>
</template>
