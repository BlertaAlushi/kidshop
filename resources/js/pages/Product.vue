<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Product } from '@/types';
import { useI18n } from 'vue-i18n';
import { Label } from '@/components/ui/label';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldDecrement,
    NumberFieldIncrement,
    NumberFieldInput,
} from '@/components/ui/number-field';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { route } from 'ziggy-js';

const { t } = useI18n();

const props = defineProps<{
    product: {
        data: Product;
    };
}>();

interface AddToCart {
    product_id: number;
    quantity: number;
}

const form = useForm<AddToCart>({
    product_id: props.product.data.id,
    quantity: 1,
});

const addToCart = () => {
    form.post(route('cart.add'));
};
</script>

<template>
    <Head :title="t('home.product')" />

    <AppLayout>
        <div class="min-h-screen bg-slate-50 px-10 py-12">
            <div class="grid grid-cols-1 items-start gap-16 md:grid-cols-2">
                <div>
                    <img
                        :src="product.data.image"
                        :alt="product.data.name"
                        class="h-150 w-full object-cover md:h-200"
                    />
                </div>

                <div class="flex h-full flex-col justify-center gap-6 pr-48">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ product.data.name }}
                    </h1>

                    <div class="text-2xl font-semibold text-primary">
                        {{ product.data.price }}
                        {{ product.data.currency }}
                    </div>

                    <NumberField
                        id="quantity"
                        :default-value="1"
                        :min="1"
                        :max="product.data.stock_quantity"
                        v-model="form.quantity"
                        class="w-fit"
                    >
                        <Label for="quantity">{{ t('home.quantity') }}</Label>
                        <NumberFieldContent>
                            <NumberFieldDecrement />
                            <NumberFieldInput/>
                            <NumberFieldIncrement />
                        </NumberFieldContent>
                    </NumberField>

                    <Button class="w-full" @click="addToCart">
                        {{ t('home.add_to_cart') }}
                    </Button>

                    <div class="w-full">
                        <Tabs default-value="description">
                            <TabsList class="mb-2">
                                <TabsTrigger value="description">
                                    {{ t('admin.description') }}
                                </TabsTrigger>
                                <TabsTrigger value="brand">
                                    {{ t('home.brand') }}
                                </TabsTrigger>
                            </TabsList>
                            <TabsContent
                                value="description"
                                class="rounded-lg bg-white p-6 shadow-sm"
                            >
                                {{ product.data.description }}
                            </TabsContent>
                            <TabsContent
                                value="brand"
                                class="rounded-lg bg-white p-6 shadow-sm"
                            >
                                {{ product.data.mark }}
                            </TabsContent>
                        </Tabs>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
