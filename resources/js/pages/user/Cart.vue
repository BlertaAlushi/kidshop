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
    cartProducts: { data: cart_product[] };
}>();

const checkOut = () => {
    router.get(route('cart.checkout'));
};
</script>

<template>
    <Head :title="t('home.cart')" />

    <AppLayout>
        <div class="w-full p-8 md:p-20">
            <div
                v-if="props.cartProducts.data.length > 0"
                class="flex flex-col items-start gap-10 md:flex-row"
            >
                <div class="flex-1 justify-items-center space-y-6">
                    <CartProduct
                        v-for="product in props.cartProducts.data"
                        :key="product.id"
                        :cart_product="product"
                    />
                </div>

                <div
                    class="flex w-full flex-col gap-4 rounded-xl bg-slate-50 p-6 md:w-64 dark:bg-gray-900"
                >
                    <p class="text-sm font-semibold">
                        {{ t('home.subtotal') }}
                    </p>
                    <p class="text-lg font-bold">
                        {{ cartTotalPrice.toFixed(2) }} €
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ t('home.checkout_description') }}
                    </p>
                    <Button class="mt-4 w-full" @click="checkOut">
                        {{ t('home.checkout') }}
                    </Button>
                </div>
            </div>

            <div v-else>
                <Empty class="rounded-xl border bg-muted/30 py-16">
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
