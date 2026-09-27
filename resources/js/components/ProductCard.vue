<script setup lang="ts">
import { ProductVariantListItem } from '@/types';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import ProductPrice from '@/components/ProductPrice.vue';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemHeader,
    ItemTitle,
} from '@/components/ui/item';

defineProps<{
    item: ProductVariantListItem;
}>();
</script>

<template>
    <Item
        class="group h-full gap-3 border-none p-0"
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
            <ItemHeader class="flex-col items-start justify-start gap-0">
                <div
                    class="aspect-square w-full overflow-hidden rounded-2xl bg-muted"
                >
                    <img
                        :src="item.image"
                        :alt="item.name"
                        width="256"
                        height="256"
                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                </div>
                <div
                    v-if="item.colors?.length"
                    class="mt-3 flex flex-wrap items-center gap-1.5"
                >
                    <span
                        v-for="color in item.colors"
                        :key="color.id"
                        :title="color.name"
                        role="button"
                        tabindex="0"
                        class="size-4 shrink-0 cursor-pointer rounded-full transition-transform hover:scale-110"
                        :class="
                            item.color?.id === color.id
                                ? 'ring-2 ring-primary ring-offset-2 ring-offset-background'
                                : 'ring-1 ring-border hover:ring-primary/50'
                        "
                        :style="{
                            backgroundColor: color.hex_code ?? '#e5e5e5',
                        }"
                        @click.stop.prevent="
                            router.get(
                                route('collection.product', item.slug) +
                                    `?color=${color.id}`,
                            )
                        "
                    />
                </div>
            </ItemHeader>
            <ItemContent class="px-0.5">
                <ItemTitle class="text-sm font-medium">{{
                    item.name
                }}</ItemTitle>
                <ItemDescription>
                    <ProductPrice
                        :price="item.price"
                        :original-price="item.original_price"
                    />
                </ItemDescription>
            </ItemContent>
        </a>
    </Item>
</template>
