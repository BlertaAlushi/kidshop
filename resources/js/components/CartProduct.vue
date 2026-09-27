<script setup lang="ts">
import { watch } from 'vue';
import { Trash } from 'lucide-vue-next';
import { type CartProduct } from '@/types';
import ProductPrice from '@/components/ProductPrice.vue';
import { router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from '@/components/ui/number-field';
import { Button } from '@/components/ui/button';
import { debounce } from 'lodash-es';
import { computed } from 'vue';

const props = defineProps<{
    cart_product: CartProduct;
}>();

const productHref = computed(() =>
    props.cart_product.color_id
        ? route('collection.product', props.cart_product.product_slug) +
          `?color=${props.cart_product.color_id}`
        : route('collection.product', props.cart_product.product_slug),
);

interface UpdateCartItem {
    quantity: number;
}

const form = useForm<UpdateCartItem>({
    quantity: props.cart_product.quantity,
});

const updateQuantity = debounce(() => {
    if (form.processing) return;

    form.post(route('cart.update', props.cart_product.id), {
        preserveScroll: true,
        preserveState: true,
    });
}, 400);

watch(() => form.quantity, updateQuantity);

const removeFromCart = () => {
    router.delete(route('cart.remove', props.cart_product.id));
};
</script>

<template>
    <div class="flex gap-4 py-6 first:pt-0">
        <a
            :href="productHref"
            class="shrink-0 overflow-hidden rounded-xl bg-muted"
        >
            <img
                v-if="cart_product.image"
                :src="cart_product.image"
                :alt="cart_product.name"
                class="h-28 w-28 object-cover"
            />
        </a>

        <div class="flex flex-1 flex-col justify-between">
            <div>
                <h3 class="text-sm font-medium">
                    <a :href="productHref">
                        {{ cart_product.name }}
                    </a>
                </h3>
                <p
                    v-if="cart_product.size || cart_product.color"
                    class="mt-1 flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <span v-if="cart_product.size">{{ cart_product.size }}</span>
                    <span v-if="cart_product.size && cart_product.color">/</span>
                    <span
                        v-if="cart_product.color"
                        :title="cart_product.color"
                        :style="{ backgroundColor: cart_product.color_hex ?? undefined }"
                        class="inline-block h-3 w-3 rounded-full border border-black/10"
                    />
                </p>
                <p class="mt-1.5 text-sm">
                    <ProductPrice
                        :price="cart_product.price"
                        :original-price="cart_product.original_price"
                    />
                </p>
            </div>

            <div class="mt-4 flex items-center justify-between gap-3">
                <NumberField
                    id="quantity"
                    :min="1"
                    :max="10"
                    v-model="form.quantity"
                    class="w-28"
                >
                    <NumberFieldContent>
                        <NumberFieldDecrement />
                        <NumberFieldInput />
                        <NumberFieldIncrement />
                    </NumberFieldContent>
                </NumberField>

                <p class="text-sm font-semibold">
                    {{ (form.quantity * cart_product.price).toFixed(2) }} €
                </p>

                <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 shrink-0 cursor-pointer text-muted-foreground hover:text-red-500"
                    @click="removeFromCart"
                >
                    <Trash class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
