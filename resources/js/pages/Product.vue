<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Product, ProductVariantOption } from '@/types';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Label } from '@/components/ui/label';
import ProductPrice from '@/components/ProductPrice.vue';
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
import { ChevronLeft, ChevronRight, Tag } from 'lucide-vue-next';
import {
    formatPromotionDiscount,
    formatPromotionEndDate,
} from '@/lib/promotion';

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

const requestedColorId = Number(
    new URLSearchParams(window.location.search).get('color'),
) || null;

const selectedColorId = ref<number | null>(
    (requestedColorId &&
        colors.value.some((color) => color.id === requestedColorId)
        ? requestedColorId
        : null) ??
        defaultVariant.value?.color?.id ??
        colors.value[0]?.id ??
        null,
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

const isColorSoldOut = (colorId: number) => {
    const colorVariants = variants.value.filter(
        (variant) => variant.color?.id === colorId,
    );

    return (
        colorVariants.length > 0 &&
        colorVariants.every((variant) => variant.stock_quantity <= 0)
    );
};

const selectVariant = (colorId: number) => {
    if (isColorSoldOut(colorId)) return;

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

const showPrevImage = () => {
    if (!galleryImages.value.length) return;
    selectedImageIndex.value =
        (selectedImageIndex.value - 1 + galleryImages.value.length) %
        galleryImages.value.length;
};

const showNextImage = () => {
    if (!galleryImages.value.length) return;
    selectedImageIndex.value =
        (selectedImageIndex.value + 1) % galleryImages.value.length;
};

const displayPrice = computed(
    () => selectedVariant.value?.price ?? props.product.data.price,
);

// A selected variant without a promotion has null original_price/promotion,
// so only fall back to the product-level values when no variant is selected.
const displayOriginalPrice = computed(() =>
    selectedVariant.value
        ? selectedVariant.value.original_price
        : props.product.data.original_price,
);

const displayPromotion = computed(() =>
    selectedVariant.value
        ? selectedVariant.value.promotion
        : props.product.data.promotion,
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
        <div class="mx-auto w-full max-w-7xl px-6 py-12">
            <div class="grid grid-cols-1 items-start gap-10 lg:grid-cols-2 lg:gap-16">
                <div>
                    <div
                        class="group relative aspect-square w-full overflow-hidden rounded-2xl bg-muted"
                    >
                        <img
                            :src="displayImage"
                            :alt="product.data.name"
                            class="h-full w-full object-cover"
                        />

                        <template v-if="galleryImages.length > 1">
                            <button
                                type="button"
                                :aria-label="t('home.previous_image')"
                                class="absolute top-1/2 left-3 flex size-9 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-background/80 text-foreground opacity-0 shadow transition hover:bg-background group-hover:opacity-100 focus-visible:opacity-100"
                                @click="showPrevImage"
                            >
                                <ChevronLeft class="size-5" />
                            </button>
                            <button
                                type="button"
                                :aria-label="t('home.next_image')"
                                class="absolute top-1/2 right-3 flex size-9 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full bg-background/80 text-foreground opacity-0 shadow transition hover:bg-background group-hover:opacity-100 focus-visible:opacity-100"
                                @click="showNextImage"
                            >
                                <ChevronRight class="size-5" />
                            </button>
                        </template>
                    </div>

                    <div
                        v-if="galleryImages.length > 1"
                        class="mt-4 flex flex-wrap gap-3"
                    >
                        <button
                            v-for="(image, index) in galleryImages"
                            :key="image"
                            type="button"
                            class="size-20 shrink-0 cursor-pointer overflow-hidden rounded-xl border-2 transition"
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

                <div class="flex h-full max-w-md flex-col gap-6">
                    <h1 class="text-3xl font-semibold tracking-tight">
                        {{ product.data.name }}
                    </h1>

                    <div class="flex flex-col gap-3">
                        <ProductPrice
                            :price="displayPrice"
                            :original-price="displayOriginalPrice"
                            size="lg"
                        />
                        <div
                            v-if="displayPromotion"
                            class="flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2.5 text-sm text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300"
                        >
                            <Tag class="mt-0.5 size-4 shrink-0" />
                            <div class="flex flex-col gap-0.5">
                                <span class="font-semibold">
                                    {{ displayPromotion.name }}:
                                    {{ formatPromotionDiscount(displayPromotion) }}
                                </span>
                                <span
                                    v-if="displayPromotion.ends_at"
                                    class="text-red-600/80 dark:text-red-300/80"
                                >
                                    {{
                                        t('home.promotion_ends', {
                                            date: formatPromotionEndDate(
                                                displayPromotion.ends_at,
                                            ),
                                        })
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="colors.length" class="flex flex-col gap-2">
                        <Label>{{ t('home.select_color') }}</Label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="color in colors"
                                :key="color.id"
                                type="button"
                                :title="
                                    isColorSoldOut(color.id)
                                        ? `${color.name} (${t('home.out_of_stock')})`
                                        : color.name
                                "
                                :disabled="isColorSoldOut(color.id)"
                                class="relative size-8 shrink-0 rounded-full transition-transform focus-visible:outline-none"
                                :class="
                                    isColorSoldOut(color.id)
                                        ? 'cursor-not-allowed ring-1 ring-border ring-offset-2 ring-offset-background opacity-40'
                                        : selectedColorId === color.id
                                          ? 'cursor-pointer ring-2 ring-primary ring-offset-2 ring-offset-background hover:scale-110'
                                          : 'cursor-pointer ring-1 ring-border ring-offset-2 ring-offset-background hover:scale-110 hover:ring-primary/50'
                                "
                                @click="selectVariant(color.id)"
                            >
                                <span
                                    class="block size-full rounded-full"
                                    :style="{
                                        backgroundColor:
                                            color.hex_code ?? '#e5e5e5',
                                    }"
                                />
                                <span
                                    v-if="isColorSoldOut(color.id)"
                                    class="pointer-events-none absolute inset-0 rounded-full"
                                    style="
                                        background: linear-gradient(
                                            to top right,
                                            transparent calc(50% - 1px),
                                            rgba(0, 0, 0, 0.6) calc(50% - 1px),
                                            rgba(0, 0, 0, 0.6) calc(50% + 1px),
                                            transparent calc(50% + 1px)
                                        );
                                    "
                                />
                            </button>
                        </div>
                    </div>

                    <div v-if="sizesForColor.length" class="flex flex-col gap-2">
                        <Label>{{ t('home.select_size') }}</Label>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="size in sizesForColor"
                                :key="size.id"
                                type="button"
                                class="min-w-11 cursor-pointer rounded-full border px-3.5 py-1.5 text-sm transition"
                                :class="
                                    selectedSizeId === size.id
                                        ? 'border-primary bg-primary text-primary-foreground'
                                        : 'border-border hover:border-foreground/40'
                                "
                                @click="selectedSizeId = size.id"
                            >
                                {{ size.name }}
                            </button>
                        </div>
                    </div>

                    <p
                        v-if="selectedVariant && !inStock"
                        class="text-sm font-medium text-red-500"
                    >
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
                        class="w-full rounded-full"
                        size="lg"
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
                                class="rounded-xl border border-border/70 bg-card p-6 text-sm text-muted-foreground"
                            >
                                {{ product.data.description }}
                            </TabsContent>
                            <TabsContent
                                value="brand"
                                class="rounded-xl border border-border/70 bg-card p-6 text-sm text-muted-foreground"
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
