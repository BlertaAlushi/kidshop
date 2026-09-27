<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type CartProduct as cart_product, type PageType } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import CartProduct from '@/components/CartProduct.vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '@/components/ui/empty';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

const { t } = useI18n();
const page = usePage<PageType>();
const cartTotalPrice = computed(() => page.props.cartTotalPrice);

const props = defineProps<{
    cartItems: { data: cart_product[] };
}>();

const checkOut = () => {
    router.get(route('cart.checkout'));
};
</script>

<template>
    <Head :title="t('home.cart')" />

    <AppLayout>
        <div class="mx-auto w-full max-w-5xl px-6 py-12">
            <h1 class="mb-8 text-2xl font-semibold tracking-tight">
                {{ t('home.cart') }}
            </h1>
            <div
                v-if="props.cartItems.data.length > 0"
                class="flex flex-col items-start gap-10 md:flex-row"
            >
                <div class="w-full flex-1 divide-y divide-border">
                    <CartProduct
                        v-for="product in props.cartItems.data"
                        :key="product.id"
                        :cart_product="product"
                    />
                </div>

                <div
                    class="flex w-full flex-col gap-4 rounded-2xl border border-border/70 bg-card p-6 md:w-72"
                >
                    <p class="text-sm font-semibold">
                        {{ t('home.subtotal') }}
                    </p>
                    <p class="text-2xl font-semibold">
                        {{ cartTotalPrice.toFixed(2) }} €
                    </p>
                    <p class="text-sm text-muted-foreground">
                        {{ t('home.checkout_description') }}
                    </p>
                    <Button
                        class="mt-4 w-full rounded-full"
                        size="lg"
                        @click="checkOut"
                    >
                        {{ t('home.checkout') }}
                    </Button>
                </div>
            </div>

            <div v-else>
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
        </div>
    </AppLayout>
</template>

<style scoped></style>
