<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Trash } from 'lucide-vue-next';
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

const emptyVariant = (): VariantForm => ({
    size_id: null,
    color_id: null,
    sku: '',
    price: 0,
    stock_quantity: 0,
    is_active: true,
});

const emptyNewImage = (): NewImageForm => ({
    file: null,
    preview: null,
    color_id: null,
    is_primary: false,
    sort_order: 0,
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

const addVariant = () => form.variants.push(emptyVariant());
const removeVariant = (index: number) => form.variants.splice(index, 1);

const removeExistingImage = (index: number) => form.existing_images.splice(index, 1);

const setExistingPrimary = (index: number) => {
    form.existing_images.forEach((image, i) => (image.is_primary = i === index));
    form.new_images.forEach((image) => (image.is_primary = false));
};

const setNewPrimary = (index: number) => {
    form.new_images.forEach((image, i) => (image.is_primary = i === index));
    form.existing_images.forEach((image) => (image.is_primary = false));
};

const addImageRow = () => form.new_images.push(emptyNewImage());
const removeNewImage = (index: number) => form.new_images.splice(index, 1);

const handleFile = (index: number, event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.new_images[index].file = file;
    form.new_images[index].preview = file ? URL.createObjectURL(file) : null;
};

const storageUrl = (path: string) => `/storage/${path}`;

const submit = () => {
    form.post(
        isEdit
            ? route('admin.products.update', props.product!.id)
            : route('admin.products.store'),
        { forceFormData: true },
    );
};
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto grid w-full grid-cols-3 gap-6 p-10">
        <!-- LEFT SIDE -->
        <div class="col-span-2 space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.product') }}</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-2 gap-4">
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.default_name') }}</Label>
                        <Input v-model="form.name" type="text" />
                        <p v-if="form.errors.name" class="text-sm text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div v-if="isEdit" class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.slug') }}</Label>
                        <Input v-model="form.slug" type="text" disabled />
                        <p v-if="form.errors.slug" class="text-sm text-red-500">
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div class="col-span-2 grid w-full items-center gap-1.5">
                        <Label>{{ t('admin.default_description') }}</Label>
                        <Textarea
                            v-model="form.description"
                            class="min-h-30"
                            :placeholder="t('admin.description')"
                        />
                        <p v-if="form.errors.description" class="text-sm text-red-500">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.category') }}</Label>
                        <Select v-model="form.category_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.select') + ' ' + t('admin.category')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.category_id" class="text-sm text-red-500">
                            {{ form.errors.category_id }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.brand') }}</Label>
                        <Select v-model="form.brand_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.select') + ' ' + t('home.brand')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.brand_id" class="text-sm text-red-500">
                            {{ form.errors.brand_id }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.gender') }}</Label>
                        <Select v-model="form.gender">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.gender')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="unisex">{{ t('admin.unisex') }}</SelectItem>
                                <SelectItem value="boy">{{ t('admin.boy') }}</SelectItem>
                                <SelectItem value="girl">{{ t('admin.girl') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.gender" class="text-sm text-red-500">
                            {{ form.errors.gender }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.seasons') }}</Label>
                        <Select v-model="form.seasons" multiple>
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.select') + ' ' + t('home.seasons')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="season in seasons" :key="season.id" :value="season.id">
                                    {{ season.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label for="is_active" class="flex items-center gap-3">
                            <Switch
                                id="is_active"
                                :model-value="form.is_active"
                                @update:model-value="form.is_active = $event"
                            />
                            <span>{{ t('admin.is_active') }}</span>
                        </Label>
                    </div>
                </CardContent>
            </Card>

            <!-- Variants -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.variants') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-for="(variant, index) in form.variants"
                        :key="index"
                        class="grid grid-cols-6 items-end gap-3 border-b pb-4 last:border-b-0"
                    >
                        <div class="grid gap-1.5">
                            <Label>{{ t('home.sizes') }}</Label>
                            <Select v-model="variant.size_id">
                                <SelectTrigger>
                                    <SelectValue :placeholder="t('admin.select')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="size in sizes" :key="size.id" :value="size.id">
                                        {{ size.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors[`variants.${index}.size_id`]" class="text-sm text-red-500">
                                {{ form.errors[`variants.${index}.size_id`] }}
                            </p>
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('home.colors') }}</Label>
                            <Select v-model="variant.color_id">
                                <SelectTrigger>
                                    <SelectValue :placeholder="t('admin.select')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                        {{ color.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="form.errors[`variants.${index}.color_id`]" class="text-sm text-red-500">
                                {{ form.errors[`variants.${index}.color_id`] }}
                            </p>
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('admin.sku') }}</Label>
                            <Input v-model="variant.sku" type="text" />
                            <p v-if="form.errors[`variants.${index}.sku`]" class="text-sm text-red-500">
                                {{ form.errors[`variants.${index}.sku`] }}
                            </p>
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('admin.price') }}</Label>
                            <Input v-model.number="variant.price" type="number" min="0" step="0.01" />
                            <p v-if="form.errors[`variants.${index}.price`]" class="text-sm text-red-500">
                                {{ form.errors[`variants.${index}.price`] }}
                            </p>
                        </div>

                        <div class="grid gap-1.5">
                            <Label>{{ t('admin.stock_quantity') }}</Label>
                            <Input v-model.number="variant.stock_quantity" type="number" min="0" />
                            <p v-if="form.errors[`variants.${index}.stock_quantity`]" class="text-sm text-red-500">
                                {{ form.errors[`variants.${index}.stock_quantity`] }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <Label class="flex items-center gap-2">
                                <Switch
                                    :model-value="variant.is_active"
                                    @update:model-value="variant.is_active = $event"
                                />
                                <span class="text-xs">{{ t('admin.is_active') }}</span>
                            </Label>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer"
                                :disabled="form.variants.length === 1"
                                @click="removeVariant(index)"
                            >
                                <Trash class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <p v-if="form.errors.variants" class="text-sm text-red-500">
                        {{ form.errors.variants }}
                    </p>

                    <Button type="button" variant="outline" class="cursor-pointer" @click="addVariant">
                        {{ t('admin.add') }} {{ t('admin.variants') }}
                    </Button>
                </CardContent>
            </Card>
        </div>

        <!-- RIGHT SIDE -->
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.images') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-for="(image, index) in form.existing_images"
                        :key="image.id"
                        class="space-y-2 border-b pb-4 last:border-b-0"
                    >
                        <img :src="storageUrl(image.path)" class="w-full rounded-lg border" alt="Product image" />

                        <Select v-model="image.color_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('home.colors')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                    {{ color.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <div class="flex items-center justify-between">
                            <Label class="flex items-center gap-2">
                                <Switch
                                    :model-value="image.is_primary"
                                    @update:model-value="setExistingPrimary(index)"
                                />
                                <span class="text-xs">{{ t('admin.primary') }}</span>
                            </Label>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer"
                                @click="removeExistingImage(index)"
                            >
                                <Trash class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <div
                        v-for="(image, index) in form.new_images"
                        :key="'new-' + index"
                        class="space-y-2 border-b pb-4 last:border-b-0"
                    >
                        <Input type="file" accept="image/*" @change="handleFile(index, $event)" />
                        <img v-if="image.preview" :src="image.preview" class="w-full rounded-lg border" alt="New product image" />
                        <p v-if="form.errors[`new_images.${index}.file`]" class="text-sm text-red-500">
                            {{ form.errors[`new_images.${index}.file`] }}
                        </p>

                        <Select v-model="image.color_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('home.colors')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                    {{ color.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        <div class="flex items-center justify-between">
                            <Label class="flex items-center gap-2">
                                <Switch
                                    :model-value="image.is_primary"
                                    @update:model-value="setNewPrimary(index)"
                                />
                                <span class="text-xs">{{ t('admin.primary') }}</span>
                            </Label>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                class="cursor-pointer"
                                @click="removeNewImage(index)"
                            >
                                <Trash class="size-4" />
                            </Button>
                        </div>
                    </div>

                    <Button type="button" variant="outline" class="w-full cursor-pointer" @click="addImageRow">
                        {{ t('admin.add') }} {{ t('admin.image') }}
                    </Button>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button @click="submit" :disabled="form.processing" class="cursor-pointer">
                    {{ isEdit ? t('admin.update') : t('admin.create') }}
                </Button>
            </div>
        </div>
    </form>
</template>
