<script setup lang="ts">
import AppLayout from '@/layouts/UserLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { type CartProduct as cart_product, type PageType } from '@/types';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemMedia,
    ItemTitle,
} from '@/components/ui/item';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import axios from 'axios';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { CheckCircle, AlertCircleIcon } from 'lucide-vue-next';

const { t } = useI18n();

defineProps<{
    cartProducts: { data: cart_product[] };
}>();

const page = usePage<PageType>();

interface Address {
    name: string;
    email: string;
    phone: string;
    address: string;
    zip: string;
    city: string;
    country: string;
}

const form = useForm<Address>({
    name: page.props.auth.user.name,
    email: page.props.auth.user.email,
    phone: '',
    address: '',
    zip: '',
    city: '',
    country: '',
});
const success = ref(false);
const errorMessage = ref<string | null>(null);
const order = async () => {
    success.value = false;
    errorMessage.value = null;
    try {
        const response = await axios.post(route('order'), form.data());
        if (!response.data.success) {
            errorMessage.value = response.data.error || 'Something went wrong';
        } else {
            success.value = true;
        }
    } catch (error: any) {
        if (error.response?.status === 422) {
            const validationErrors = error.response.data.errors;
            for (const key in validationErrors) {
                const k = key as keyof Address;
                form.errors[k] = validationErrors[key][0];
            }
            errorMessage.value = 'Please fix the highlighted errors.';
        } else {
            errorMessage.value = error.message || 'Something went wrong.';
        }
    }
};
</script>

<template>
    <Head :title="t('home.checkout')" />

    <AppLayout>
        <div v-if="success" class="mt-20 flex justify-center">
            <Alert
                class="flex w-full max-w-md items-center gap-3 rounded-lg border border-green-600 bg-white p-6 text-green-600 shadow-lg"
            >
                <CheckCircle class="h-6 w-6" />
                <div>
                    <AlertTitle class="text-lg font-semibold">
                        {{ t('home.order_success') }}
                    </AlertTitle>
                </div>
            </Alert>
        </div>
        <div v-else class="flex min-h-screen justify-center bg-slate-50 py-12">
            <div class="w-full max-w-6xl px-10">
                <div class="grid grid-cols-1 items-start gap-16 md:grid-cols-2">
                    <div class="mx-auto flex w-full max-w-md flex-col gap-6">
                        <Alert
                            v-if="errorMessage"
                            variant="destructive"
                            class="max-w-md"
                        >
                            <AlertCircleIcon />
                            <AlertTitle>{{ t('home.order_error') }}</AlertTitle>
                            <AlertDescription>
                                {{ errorMessage }}
                            </AlertDescription>
                        </Alert>
                        <ItemGroup class="gap-4">
                            <Item
                                v-for="product in cartProducts.data"
                                :key="product.id"
                                asChild
                                role="listitem"
                                variant="outline"
                            >
                                <span>
                                    <ItemMedia variant="image">
                                        <img
                                            :src="product.image"
                                            :alt="product.name"
                                            width="32"
                                            height="32"
                                            class="object-cover grayscale"
                                        />
                                    </ItemMedia>

                                    <ItemContent>
                                        <ItemTitle class="line-clamp-1">
                                            {{ product.name }}
                                        </ItemTitle>
                                        <ItemDescription>
                                            {{ product.quantity }}x
                                            {{ product.price }}
                                            {{ product.currency }}
                                        </ItemDescription>
                                    </ItemContent>

                                    <ItemContent class="flex-none text-center">
                                        <ItemTitle>
                                            {{
                                                (
                                                    product.quantity *
                                                    product.price
                                                ).toFixed(2)
                                            }}
                                            {{ product.currency }}
                                        </ItemTitle>
                                    </ItemContent>
                                </span>
                            </Item>

                            <Item asChild role="listitem">
                                <span>
                                    <ItemContent>
                                        <ItemTitle class="text-lg font-bold">
                                            Total
                                        </ItemTitle>
                                    </ItemContent>
                                    <ItemContent>
                                        <ItemTitle class="text-lg font-bold">
                                            {{
                                                page.props.cartTotalPrice.toFixed(
                                                    2,
                                                )
                                            }}
                                            €
                                        </ItemTitle>
                                    </ItemContent>
                                </span>
                            </Item>
                        </ItemGroup>
                    </div>

                    <div class="mx-auto flex w-full max-w-xl flex-col gap-6">
                        <Card class="w-full">
                            <CardHeader>
                                <CardTitle>
                                    {{ t('home.address.customer') }}
                                </CardTitle>
                            </CardHeader>

                            <CardContent class="space-y-6">
                                <div class="grid w-full gap-1.5">
                                    <Label>{{ t('home.address.name') }}</Label>
                                    <Input v-model="form.name" type="text" />
                                    <p
                                        v-if="form.errors.name"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{ t('home.address.email') }}</Label>
                                    <Input v-model="form.email" type="text" />
                                    <p
                                        v-if="form.errors.email"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{ t('home.address.phone') }}</Label>
                                    <Input v-model="form.phone" type="text" />
                                    <p
                                        v-if="form.errors.phone"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{
                                        t('home.address.address')
                                    }}</Label>
                                    <Input v-model="form.address" type="text" />
                                    <p
                                        v-if="form.errors.address"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.address }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{ t('home.address.zip') }}</Label>
                                    <Input v-model="form.zip" type="text" />
                                    <p
                                        v-if="form.errors.zip"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.zip }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{ t('home.address.city') }}</Label>
                                    <Input v-model="form.city" type="text" />
                                    <p
                                        v-if="form.errors.city"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.city }}
                                    </p>
                                </div>

                                <div class="grid w-full gap-1.5">
                                    <Label>{{
                                        t('home.address.country')
                                    }}</Label>
                                    <Input v-model="form.country" type="text" />
                                    <p
                                        v-if="form.errors.country"
                                        class="text-sm text-red-500"
                                    >
                                        {{ form.errors.country }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <Button
                            class="w-full cursor-pointer"
                            :disabled="form.processing"
                            @click="order"
                        >
                            {{ t('home.order') }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
