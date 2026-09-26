<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Product, ProductVariantOption } from '@/types';
import { computed, ref, watch } from 'vue';
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

const variants = computed(() => props.product.data.variants ?? []);
const colors = computed(() => props.product.data.colors ?? []);

const defaultVariant = computed<ProductVariantOption | null>(
    () =>
        variants.value.find(
            (variant) => variant.id === props.product.data.default_variant?.id,
        ) ?? null,
);

const selectedColorId = ref<number | null>(
    defaultVariant.value?.color?.id ?? colors.value[0]?.id ?? null,
);

const sizesForColor = computed(() => {
    const seen = new Map<number, { id: number; name: string; sort_order: number }>();

    variants.value
        .filter((variant) =>
            colors.value.length
                ? variant.color?.id === selectedColorId.value
                : true,
        )
        .forEach((variant) => {
            if (variant.size && !seen.has(variant.size.id)) {
                seen.set(variant.size.id, variant.size);
            }
        });

    return [...seen.values()].sort((a, b) => a.sort_order - b.sort_order);
});

const selectedSizeId = ref<number | null>(
    defaultVariant.value?.size?.id ?? sizesForColor.value[0]?.id ?? null,
);

const selectVariant = (colorId: number) => {
    selectedColorId.value = colorId;
    const firstSize = sizesForColor.value[0];
    selectedSizeId.value = firstSize?.id ?? null;
};

const selectedVariant = computed<ProductVariantOption | null>(() => {
    return (
        variants.value.find((variant) => {
            const matchesColor = colors.value.length
                ? variant.color?.id === selectedColorId.value
                : true;
            const matchesSize = sizesForColor.value.length
                ? variant.size?.id === selectedSizeId.value
                : true;
            return matchesColor && matchesSize;
        }) ?? defaultVariant.value
    );
});

const galleryImages = computed(() => {
    const color = colors.value.find((c) => c.id === selectedColorId.value);
    if (color?.images?.length) return color.images;
    return props.product.data.image ? [props.product.data.image] : [];
});

const selectedImageIndex = ref(0);

watch(galleryImages, () => {
    selectedImageIndex.value = 0;
});

const displayImage = computed(
    () => galleryImages.value[selectedImageIndex.value] ?? props.product.data.image,
);

const displayPrice = computed(
    () => selectedVariant.value?.price ?? props.product.data.price,
);

const inStock = computed(
    () => (selectedVariant.value?.stock_quantity ?? 0) > 0,
);

interface AddToCart {
    product_variant_id: number;
    quantity: number;
}

const form = useForm<AddToCart>({
    product_variant_id: selectedVariant.value?.id ?? 0,
    quantity: 1,
});

const addToCart = () => {
    form.product_variant_id = selectedVariant.value?.id ?? 0;
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
                        :src="displayImage"
                        :alt="product.data.name"
                        class="h-150 w-full object-cover md:h-200"
                    />

                    <div
                        v-if="galleryImages.length > 1"
                        class="mt-4 flex flex-wrap gap-3"
                    >
                        <button
                            v-for="(image, index) in galleryImages"
                            :key="image"
                            type="button"
                            class="size-20 shrink-0 cursor-pointer overflow-hidden rounded-md border-2 transition"
                            :class="
                                selectedImageIndex === index
                                    ? 'border-primary'
                                    : 'border-black/10'
                            "
                            @click="selectedImageIndex = index"
                        >
                            <img
                                :src="image"
                                :alt="product.data.name"
                                class="h-full w-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <div class="flex h-full flex-col justify-center gap-6 pr-48">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ product.data.name }}
                    </h1>

                    <div class="text-2xl font-semibold text-primary">
                        {{ displayPrice }} €
                    </div>

                    <div v-if="colors.length" class="flex flex-col gap-2">
                        <Label>{{ t('home.select_color') }}</Label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="color in colors"
                                :key="color.id"
                                type="button"
                                :title="color.name"
                                class="size-8 cursor-pointer rounded-sm border-2 transition"
                                :class="
                                    selectedColorId === color.id
                                        ? 'border-primary'
                                        : 'border-black/10'
                                "
                                :style="{
                                    backgroundColor: color.hex_code ?? '#e5e5e5',
                                }"
                                @click="selectVariant(color.id)"
                            />
                        </div>
                    </div>

                    <div v-if="sizesForColor.length" class="flex flex-col gap-2">
                        <Label>{{ t('home.select_size') }}</Label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="size in sizesForColor"
                                :key="size.id"
                                type="button"
                                class="min-w-10 cursor-pointer rounded-md border px-3 py-1.5 text-sm transition"
                                :class="
                                    selectedSizeId === size.id
                                        ? 'border-primary bg-primary text-primary-foreground'
                                        : 'border-black/10'
                                "
                                @click="selectedSizeId = size.id"
                            >
                                {{ size.name }}
                            </button>
                        </div>
                    </div>

                    <p v-if="selectedVariant && !inStock" class="text-sm text-red-500">
                        {{ t('home.out_of_stock') }}
                    </p>

                    <NumberField
                        id="quantity"
                        :default-value="1"
                        :min="1"
                        :max="selectedVariant?.stock_quantity ?? 1"
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

                    <Button
                        class="w-full"
                        :disabled="!selectedVariant || !inStock"
                        @click="addToCart"
                    >
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
                                {{ product.data.brand }}
                            </TabsContent>
                        </Tabs>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
