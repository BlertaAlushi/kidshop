<script setup lang="ts">
import { computed, onBeforeUnmount, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { AlertCircle, ArrowLeft, Check, Copy, ImagePlus, Plus, Star, Trash2, X } from 'lucide-vue-next';
import {
    AdminProduct,
    Brand,
    Category,
    Color,
    Season,
    Size,
} from '@/types';

const { t } = useI18n();

interface VariantForm {
    id?: number;
    size_id: number | null;
    color_id: number | null;
    sku: string;
    price: number;
    stock_quantity: number;
    is_active: boolean;
}

interface ExistingImageForm {
    id: number;
    path: string;
    color_id: number | null;
    is_primary: boolean;
    sort_order: number;
}

interface NewImageForm {
    file: File | null;
    preview: string | null;
    color_id: number | null;
    is_primary: boolean;
    sort_order: number;
}

interface ProductFormData {
    category_id: number | null;
    brand_id: number | null;
    name: string;
    slug: string;
    description: string;
    gender: 'boy' | 'girl' | 'unisex';
    is_active: boolean;
    seasons: number[];
    variants: VariantForm[];
    existing_images: ExistingImageForm[];
    new_images: NewImageForm[];
}

const props = defineProps<{
    product?: AdminProduct;
    categories: Category[];
    brands: Brand[];
    sizes: Size[];
    colors: Color[];
    seasons: Season[];
}>();

const isEdit = !!props.product?.id;

const genders = [
    { value: 'boy', active: 'border-sky-300 bg-sky-50 text-sky-800 dark:border-sky-800 dark:bg-sky-950/50 dark:text-sky-300' },
    { value: 'girl', active: 'border-pink-300 bg-pink-50 text-pink-800 dark:border-pink-800 dark:bg-pink-950/50 dark:text-pink-300' },
    { value: 'unisex', active: 'border-violet-300 bg-violet-50 text-violet-800 dark:border-violet-800 dark:bg-violet-950/50 dark:text-violet-300' },
] as const;

const emptyVariant = (): VariantForm => ({
    size_id: null,
    color_id: null,
    sku: '',
    price: 0,
    stock_quantity: 0,
    is_active: true,
});

const form = useForm<ProductFormData>({
    category_id: props.product?.category_id ?? null,
    brand_id: props.product?.brand_id ?? null,
    name: props.product?.name ?? '',
    slug: props.product?.slug ?? '',
    description: props.product?.description ?? '',
    gender: props.product?.gender ?? 'unisex',
    is_active: props.product?.is_active ?? true,

    seasons: props.product?.seasons?.map((season) => season.id) ?? [],

    variants: props.product?.variants?.length
        ? props.product.variants.map((variant) => ({
              id: variant.id,
              size_id: variant.size_id,
              color_id: variant.color_id,
              sku: variant.sku,
              price: variant.price,
              stock_quantity: variant.stock_quantity,
              is_active: variant.is_active,
          }))
        : [emptyVariant()],

    existing_images: props.product?.images?.map((image) => ({
        id: image.id,
        path: image.path,
        color_id: image.color_id,
        is_primary: image.is_primary,
        sort_order: image.sort_order,
    })) ?? [],

    new_images: [],
});

const hasErrors = computed(() => Object.keys(form.errors).length > 0);
const errorFor = (key: string) => (form.errors as Record<string, string | undefined>)[key];

const totalStock = computed(() =>
    form.variants.reduce((sum, variant) => sum + (Number(variant.stock_quantity) || 0), 0),
);

// Seasons
const toggleSeason = (id: number) => {
    const index = form.seasons.indexOf(id);
    if (index === -1) form.seasons.push(id);
    else form.seasons.splice(index, 1);
};

// Variants
const addVariant = () => form.variants.push(emptyVariant());
const duplicateVariant = (index: number) => {
    const source = form.variants[index];
    form.variants.splice(index + 1, 0, {
        ...emptyVariant(),
        size_id: source.size_id,
        color_id: source.color_id,
        price: source.price,
        is_active: source.is_active,
    });
};
const removeVariant = (index: number) => form.variants.splice(index, 1);

// Images
const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const imageCount = computed(() => form.existing_images.length + form.new_images.length);
const hasPrimary = () =>
    form.existing_images.some((image) => image.is_primary) || form.new_images.some((image) => image.is_primary);

const addFiles = (files: FileList | null | undefined) => {
    if (!files) return;
    Array.from(files)
        .filter((file) => file.type.startsWith('image/'))
        .forEach((file) => {
            form.new_images.push({
                file,
                preview: URL.createObjectURL(file),
                color_id: null,
                is_primary: !hasPrimary(),
                sort_order: imageCount.value,
            });
        });
};

const onFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    addFiles(input.files);
    input.value = '';
};

const onDrop = (event: DragEvent) => {
    isDragging.value = false;
    addFiles(event.dataTransfer?.files);
};

const setExistingPrimary = (index: number) => {
    form.existing_images.forEach((image, i) => (image.is_primary = i === index));
    form.new_images.forEach((image) => (image.is_primary = false));
};

const setNewPrimary = (index: number) => {
    form.new_images.forEach((image, i) => (image.is_primary = i === index));
    form.existing_images.forEach((image) => (image.is_primary = false));
};

const ensurePrimary = () => {
    if (hasPrimary()) return;
    if (form.existing_images.length) form.existing_images[0].is_primary = true;
    else if (form.new_images.length) form.new_images[0].is_primary = true;
};

const removeExistingImage = (index: number) => {
    form.existing_images.splice(index, 1);
    ensurePrimary();
};

const removeNewImage = (index: number) => {
    const [removed] = form.new_images.splice(index, 1);
    if (removed?.preview) URL.revokeObjectURL(removed.preview);
    ensurePrimary();
};

onBeforeUnmount(() => {
    form.new_images.forEach((image) => image.preview && URL.revokeObjectURL(image.preview));
});

// Select can't hold a null item value, so "all colors" uses a sentinel
const NO_COLOR = 'none';
const toColorValue = (id: number | null) => id ?? NO_COLOR;
const fromColorValue = (value: unknown) => (value === NO_COLOR ? null : (value as number));

const storageUrl = (path: string) => `/storage/${path}`;

const submit = () => {
    form.post(
        isEdit
            ? route('admin.products.update', props.product!.id)
            : route('admin.products.store'),
        { forceFormData: true, preserveScroll: true },
    );
};
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto flex w-full max-w-7xl flex-col gap-5 p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <Button variant="outline" size="icon" class="size-9 shrink-0" as-child>
                    <Link :href="route('admin.products.index')" :aria-label="t('admin.products.back')">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-semibold tracking-tight">
                        {{ isEdit ? product!.name : t('admin.products.new') }}
                    </h1>
                    <p class="truncate text-sm text-muted-foreground">
                        {{ isEdit ? '/' + product!.slug : t('admin.products.subtitle_new') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <Link :href="route('admin.products.index')">{{ t('admin.table.cancel') }}</Link>
                </Button>
                <Button type="submit" :disabled="form.processing" class="cursor-pointer">
                    <Check class="size-4" />
                    {{ isEdit ? t('admin.products.save_changes') : t('admin.products.save') }}
                </Button>
            </div>
        </div>

        <!-- Error summary -->
        <div
            v-if="hasErrors"
            class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-300"
        >
            <AlertCircle class="size-4 shrink-0" />
            <span>{{ t('admin.products.errors_title') }}</span>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <!-- MAIN COLUMN -->
            <div class="space-y-5 lg:col-span-2">
                <!-- Basic info -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.products.basic_info') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-1.5">
                            <Label for="name">{{ t('admin.name') }}</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :placeholder="t('admin.products.name_placeholder')"
                                :aria-invalid="!!form.errors.name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="description">{{ t('admin.description') }}</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="min-h-32"
                                :placeholder="t('admin.products.description_placeholder')"
                            />
                            <InputError :message="form.errors.description" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Images -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            {{ t('admin.images') }}
                            <span class="rounded-full bg-muted px-2 py-0.5 text-xs font-normal text-muted-foreground">
                                {{ imageCount }}
                            </span>
                        </CardTitle>
                        <CardDescription>{{ t('admin.products.images_hint') }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                            <!-- Existing images -->
                            <div
                                v-for="(image, index) in form.existing_images"
                                :key="image.id"
                                class="group flex flex-col gap-2"
                            >
                                <div
                                    class="relative aspect-square overflow-hidden rounded-lg border bg-muted"
                                    :class="image.is_primary && 'ring-2 ring-primary ring-offset-2 ring-offset-background'"
                                >
                                    <img :src="storageUrl(image.path)" class="size-full object-cover" alt="" />
                                    <button
                                        type="button"
                                        class="absolute top-1.5 left-1.5 flex size-7 cursor-pointer items-center justify-center rounded-full bg-background/90 shadow-sm transition hover:bg-background"
                                        :title="t('admin.products.set_primary')"
                                        @click="setExistingPrimary(index)"
                                    >
                                        <Star
                                            class="size-3.5"
                                            :class="image.is_primary ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground'"
                                        />
                                    </button>
                                    <button
                                        type="button"
                                        class="absolute top-1.5 right-1.5 flex size-7 cursor-pointer items-center justify-center rounded-full bg-background/90 text-muted-foreground shadow-sm transition hover:bg-red-50 hover:text-red-600 sm:opacity-0 sm:group-hover:opacity-100 dark:hover:bg-red-950"
                                        :title="t('admin.table.delete')"
                                        @click="removeExistingImage(index)"
                                    >
                                        <X class="size-3.5" />
                                    </button>
                                    <span
                                        v-if="image.is_primary"
                                        class="absolute bottom-1.5 left-1.5 rounded-full bg-primary px-2 py-0.5 text-[10px] font-medium text-primary-foreground"
                                    >
                                        {{ t('admin.primary') }}
                                    </span>
                                </div>
                                <Select
                                    :model-value="toColorValue(image.color_id)"
                                    @update:model-value="image.color_id = fromColorValue($event)"
                                >
                                    <SelectTrigger size="sm" class="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem :value="NO_COLOR">{{ t('admin.products.all_colors') }}</SelectItem>
                                        <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                            <span
                                                class="size-3 rounded-full border"
                                                :style="{ backgroundColor: color.hex_code ?? 'transparent' }"
                                            />
                                            {{ color.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- New images -->
                            <div
                                v-for="(image, index) in form.new_images"
                                :key="image.preview ?? index"
                                class="group flex flex-col gap-2"
                            >
                                <div
                                    class="relative aspect-square overflow-hidden rounded-lg border bg-muted"
                                    :class="[
                                        image.is_primary && 'ring-2 ring-primary ring-offset-2 ring-offset-background',
                                        errorFor(`new_images.${index}.file`) && 'border-red-500',
                                    ]"
                                >
                                    <img v-if="image.preview" :src="image.preview" class="size-full object-cover" alt="" />
                                    <button
                                        type="button"
                                        class="absolute top-1.5 left-1.5 flex size-7 cursor-pointer items-center justify-center rounded-full bg-background/90 shadow-sm transition hover:bg-background"
                                        :title="t('admin.products.set_primary')"
                                        @click="setNewPrimary(index)"
                                    >
                                        <Star
                                            class="size-3.5"
                                            :class="image.is_primary ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground'"
                                        />
                                    </button>
                                    <button
                                        type="button"
                                        class="absolute top-1.5 right-1.5 flex size-7 cursor-pointer items-center justify-center rounded-full bg-background/90 text-muted-foreground shadow-sm transition hover:bg-red-50 hover:text-red-600 sm:opacity-0 sm:group-hover:opacity-100 dark:hover:bg-red-950"
                                        :title="t('admin.table.delete')"
                                        @click="removeNewImage(index)"
                                    >
                                        <X class="size-3.5" />
                                    </button>
                                    <span
                                        class="absolute bottom-1.5 left-1.5 rounded-full px-2 py-0.5 text-[10px] font-medium"
                                        :class="image.is_primary ? 'bg-primary text-primary-foreground' : 'bg-background/90 text-foreground'"
                                    >
                                        {{ image.is_primary ? t('admin.primary') : t('admin.new') }}
                                    </span>
                                </div>
                                <InputError :message="errorFor(`new_images.${index}.file`)" class="text-xs" />
                                <Select
                                    :model-value="toColorValue(image.color_id)"
                                    @update:model-value="image.color_id = fromColorValue($event)"
                                >
                                    <SelectTrigger size="sm" class="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem :value="NO_COLOR">{{ t('admin.products.all_colors') }}</SelectItem>
                                        <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                            <span
                                                class="size-3 rounded-full border"
                                                :style="{ backgroundColor: color.hex_code ?? 'transparent' }"
                                            />
                                            {{ color.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Dropzone -->
                            <button
                                type="button"
                                class="flex aspect-square cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed p-3 text-center text-muted-foreground transition hover:border-primary/50 hover:bg-muted/50 hover:text-foreground"
                                :class="[
                                    isDragging && 'border-primary bg-primary/5 text-foreground',
                                    imageCount === 0 && 'col-span-2 aspect-auto py-10 sm:col-span-3 md:col-span-4',
                                ]"
                                @click="fileInput?.click()"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="onDrop"
                            >
                                <div class="flex size-10 items-center justify-center rounded-full bg-muted">
                                    <ImagePlus class="size-5" />
                                </div>
                                <span class="text-sm font-medium">{{ t('admin.products.upload') }}</span>
                                <span v-if="imageCount === 0" class="text-xs">{{ t('admin.products.drop_here') }}</span>
                            </button>
                        </div>
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                            class="hidden"
                            @change="onFileChange"
                        />
                    </CardContent>
                </Card>

                <!-- Variants -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader class="flex flex-row flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1.5">
                            <CardTitle class="flex items-center gap-2">
                                {{ t('admin.variants') }}
                                <span class="rounded-full bg-muted px-2 py-0.5 text-xs font-normal text-muted-foreground">
                                    {{ form.variants.length }}
                                </span>
                            </CardTitle>
                            <CardDescription>{{ t('admin.products.variants_hint') }}</CardDescription>
                        </div>
                        <span class="text-sm text-muted-foreground">
                            {{ t('admin.products.total_stock', { count: totalStock }) }}
                        </span>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <!-- Column headers (desktop) -->
                        <div
                            class="hidden grid-cols-[1fr_1.2fr_1.2fr_0.9fr_0.8fr_auto] gap-3 px-3 text-xs font-medium tracking-wide text-muted-foreground uppercase md:grid"
                        >
                            <span>{{ t('admin.products.size') }}</span>
                            <span>{{ t('admin.products.color') }}</span>
                            <span>{{ t('admin.sku') }}</span>
                            <span>{{ t('admin.price') }}</span>
                            <span>{{ t('admin.products.stock') }}</span>
                            <span class="w-[124px]" />
                        </div>

                        <div
                            v-for="(variant, index) in form.variants"
                            :key="variant.id ?? 'new-' + index"
                            class="grid grid-cols-2 gap-3 rounded-lg border p-3 transition md:grid-cols-[1fr_1.2fr_1.2fr_0.9fr_0.8fr_auto] md:items-start"
                            :class="!variant.is_active && 'bg-muted/40'"
                        >
                            <div class="grid gap-1.5">
                                <Label class="text-xs text-muted-foreground md:hidden">{{ t('admin.products.size') }}</Label>
                                <Select v-model="variant.size_id">
                                    <SelectTrigger
                                        class="w-full"
                                        :aria-invalid="!!errorFor(`variants.${index}.size_id`)"
                                    >
                                        <SelectValue :placeholder="t('admin.select')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="size in sizes" :key="size.id" :value="size.id">
                                            {{ size.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errorFor(`variants.${index}.size_id`)" />
                            </div>

                            <div class="grid gap-1.5">
                                <Label class="text-xs text-muted-foreground md:hidden">{{ t('admin.products.color') }}</Label>
                                <Select v-model="variant.color_id">
                                    <SelectTrigger
                                        class="w-full"
                                        :aria-invalid="!!errorFor(`variants.${index}.color_id`)"
                                    >
                                        <SelectValue :placeholder="t('admin.select')" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                            <span
                                                class="size-3 rounded-full border"
                                                :style="{ backgroundColor: color.hex_code ?? 'transparent' }"
                                            />
                                            {{ color.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errorFor(`variants.${index}.color_id`)" />
                            </div>

                            <div class="col-span-2 grid gap-1.5 md:col-span-1">
                                <Label class="text-xs text-muted-foreground md:hidden">{{ t('admin.sku') }}</Label>
                                <Input
                                    v-model="variant.sku"
                                    type="text"
                                    placeholder="SKU-001"
                                    :aria-invalid="!!errorFor(`variants.${index}.sku`)"
                                />
                                <InputError :message="errorFor(`variants.${index}.sku`)" />
                            </div>

                            <div class="grid gap-1.5">
                                <Label class="text-xs text-muted-foreground md:hidden">{{ t('admin.price') }}</Label>
                                <Input
                                    v-model.number="variant.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="tabular-nums"
                                    :aria-invalid="!!errorFor(`variants.${index}.price`)"
                                />
                                <InputError :message="errorFor(`variants.${index}.price`)" />
                            </div>

                            <div class="grid gap-1.5">
                                <Label class="text-xs text-muted-foreground md:hidden">{{ t('admin.products.stock') }}</Label>
                                <Input
                                    v-model.number="variant.stock_quantity"
                                    type="number"
                                    min="0"
                                    class="tabular-nums"
                                    :aria-invalid="!!errorFor(`variants.${index}.stock_quantity`)"
                                />
                                <InputError :message="errorFor(`variants.${index}.stock_quantity`)" />
                            </div>

                            <div class="col-span-2 flex h-9 items-center justify-between gap-1 border-t pt-3 md:col-span-1 md:border-t-0 md:pt-0">
                                <Label class="flex cursor-pointer items-center gap-2 pr-1" :title="t('admin.is_active')">
                                    <Switch
                                        :model-value="variant.is_active"
                                        @update:model-value="variant.is_active = $event"
                                    />
                                    <span class="text-xs text-muted-foreground md:sr-only">{{ t('admin.is_active') }}</span>
                                </Label>
                                <div class="flex">
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 cursor-pointer text-muted-foreground hover:text-foreground"
                                        :title="t('admin.products.duplicate')"
                                        @click="duplicateVariant(index)"
                                    >
                                        <Copy class="size-4" />
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-8 cursor-pointer text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                        :title="t('admin.table.delete')"
                                        :disabled="form.variants.length === 1"
                                        @click="removeVariant(index)"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <InputError :message="form.errors.variants" />

                        <Button
                            type="button"
                            variant="outline"
                            class="w-full cursor-pointer border-dashed"
                            @click="addVariant"
                        >
                            <Plus class="size-4" />
                            {{ t('admin.products.add_variant') }}
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- SIDE COLUMN -->
            <div class="space-y-5">
                <!-- Status -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.products.status') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Label
                            for="is_active"
                            class="flex cursor-pointer items-center justify-between gap-4 rounded-lg border p-3"
                        >
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2 font-medium">
                                    <span
                                        class="size-2 rounded-full"
                                        :class="form.is_active ? 'bg-green-500' : 'bg-gray-400'"
                                    />
                                    {{ form.is_active ? t('admin.table.active') : t('admin.table.inactive') }}
                                </div>
                                <p class="text-xs font-normal text-muted-foreground">
                                    {{ t('admin.products.active_hint') }}
                                </p>
                            </div>
                            <Switch
                                id="is_active"
                                :model-value="form.is_active"
                                @update:model-value="form.is_active = $event"
                            />
                        </Label>
                    </CardContent>
                </Card>

                <!-- Organization -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.products.organization') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-1.5">
                            <Label>{{ t('admin.category') }}</Label>
                            <Select v-model="form.category_id">
                                <SelectTrigger class="w-full" :aria-invalid="!!form.errors.category_id">
                                    <SelectValue :placeholder="t('admin.select') + ' ' + t('admin.category').toLowerCase()" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.category_id" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('home.brand') }}</Label>
                            <Select v-model="form.brand_id">
                                <SelectTrigger class="w-full" :aria-invalid="!!form.errors.brand_id">
                                    <SelectValue :placeholder="t('admin.select') + ' ' + t('home.brand').toLowerCase()" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">
                                        {{ brand.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.brand_id" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('admin.gender') }}</Label>
                            <div class="grid grid-cols-3 gap-2" role="radiogroup">
                                <button
                                    v-for="gender in genders"
                                    :key="gender.value"
                                    type="button"
                                    role="radio"
                                    :aria-checked="form.gender === gender.value"
                                    class="h-9 cursor-pointer rounded-md border text-sm font-medium transition"
                                    :class="form.gender === gender.value ? gender.active : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                                    @click="form.gender = gender.value"
                                >
                                    {{ t('admin.' + gender.value) }}
                                </button>
                            </div>
                            <InputError :message="form.errors.gender" />
                        </div>

                        <div v-if="seasons.length" class="grid gap-1.5">
                            <Label>{{ t('home.seasons') }}</Label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="season in seasons"
                                    :key="season.id"
                                    type="button"
                                    :aria-pressed="form.seasons.includes(season.id)"
                                    class="inline-flex h-8 cursor-pointer items-center gap-1.5 rounded-full border px-3 text-sm transition"
                                    :class="
                                        form.seasons.includes(season.id)
                                            ? 'border-primary bg-primary text-primary-foreground'
                                            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                    "
                                    @click="toggleSeason(season.id)"
                                >
                                    <Check v-if="form.seasons.includes(season.id)" class="size-3.5" />
                                    {{ season.name }}
                                </button>
                            </div>
                            <InputError :message="form.errors.seasons" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Footer actions -->
        <div class="flex justify-end gap-2 border-t pt-5">
            <Button variant="outline" as-child>
                <Link :href="route('admin.products.index')">{{ t('admin.table.cancel') }}</Link>
            </Button>
            <Button type="submit" :disabled="form.processing" class="cursor-pointer">
                <Check class="size-4" />
                {{ isEdit ? t('admin.products.save_changes') : t('admin.products.save') }}
            </Button>
        </div>
    </form>
</template>
