<script setup lang="ts">
import { defineProps, watch } from 'vue';
import { Trash } from 'lucide-vue-next';
import { type CartProduct } from '@/types';
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

const props = defineProps<{
    cart_product: CartProduct;
}>();

interface UpdateCartProduct {
    product_id: number;
    quantity: number;
}

const form = useForm<UpdateCartProduct>({
    product_id: props.cart_product.product_id,
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
    <div class="w-2/3 flex gap-4 border-b border-gray-200 py-8 dark:border-gray-700">
        <img
            :src="cart_product.image"
            :alt="cart_product.name"
            class="h-36 w-36 object-cover md:h-36 md:w-36"
        />

        <div class="flex flex-1 flex-col justify-between">
            <div>
                <h3
                    class="text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                    {{ cart_product.name }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ cart_product.price }} {{ cart_product.currency }}
                </p>
            </div>

            <div class="mt-4 flex items-center justify-between">
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
                    {{ (form.quantity * cart_product.price).toFixed(2) }}
                    {{ cart_product.currency }}
                </p>

                <Button
                    variant="ghost"
                    size="icon"
                    class="h-8 w-8 text-gray-400 hover:text-red-500"
                    @click="removeFromCart"
                >
                    <Trash class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
