<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';
import { type Product } from '@/types';

const props = defineProps<{
    product: Product;
}>();

const emit = defineEmits<{
    (e: 'cartProductsQuantity', quantity: number): void;
}>();

const quantity = ref(1);

function increase() {
    quantity.value++;
}

function decrease() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

async function addToCart() {
    if (!props.product.id) return;

    try {
        const response = await axios.post('/add-to-cart', {
            product_id: props.product.id,
            quantity: quantity.value,
        });
        if (response.data.success) {
            emit('cartProductsQuantity', response.data.cart_products_quantity);
        }
        console.log(response);
    } catch (error) {
        console.log(error);
    }
}
</script>

<template>
    <div
        class="flex flex-col rounded-xl border border-gray-200 p-4 shadow-sm transition hover:shadow-md dark:border-gray-700"
    >
        <div
            class="mb-3 flex aspect-video items-center justify-center rounded-lg bg-gray-100 text-gray-400 dark:bg-gray-800"
        >
            Image
        </div>

        <h3 class="mb-1 text-lg font-semibold">
            {{ props.product.name }}
        </h3>

        <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">
            Price:
            <span class="font-medium text-gray-900 dark:text-white">
                {{ props.product.price }} {{ props.product.currency }}
            </span>
        </p>

        <div
            class="mt-3 mb-3 flex w-full items-center justify-between gap-3 rounded-lg bg-gray-100"
        >
            <button
                class="flex h-8 w-15 cursor-pointer items-center justify-center rounded-l-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600"
                @click="decrease"
            >
                -
            </button>

            <span class="text-small font-small w-8 text-center">
                {{ quantity }}
            </span>

            <button
                class="flex h-8 w-15 cursor-pointer items-center justify-center rounded-r-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600"
                @click="increase"
            >
                +
            </button>
        </div>

        <button
            class="mt-auto cursor-pointer rounded-lg bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
            @click="addToCart"
        >
            Add to Cart
        </button>
    </div>
</template>

<style scoped></style>
