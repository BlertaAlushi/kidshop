<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Product } from '@/types';
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
        data: Product[];
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
                        v-for="product in new_arrivals.data"
                        :key="product.name"
                        variant="outline"
                        as-child
                        role="listitem"
                    >
                        <a :href="route('collection.product', product.slug)">
                            <ItemHeader>
                                <img
                                    :src="product.image"
                                    :alt="product.name"
                                    width="128"
                                    height="128"
                                    class="aspect-square w-full rounded-sm object-cover"
                                />
                            </ItemHeader>
                            <ItemContent>
                                <ItemTitle>{{ product.name }}</ItemTitle>
                                <ItemDescription
                                    >{{ product.price }}
                                    {{ product.currency }}
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
