<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import InputError from '@/components/InputError.vue';
import PromotionProductPicker from '@/components/PromotionProductPicker.vue';
import {
    AlertCircle,
    ArrowLeft,
    CalendarDays,
    Check,
    Euro,
    ImageOff,
    LayoutGrid,
    Package,
    Percent,
    Plus,
    Search,
    Tag,
    Trash2,
    X,
} from 'lucide-vue-next';
import { Color, Promotion, PromotionProductOption, PromotionTargetForm } from '@/types';

const { t } = useI18n();

interface OptionItem {
    id: number;
    name: string;
}

interface PromotionFormData {
    name: string;
    type: 'percentage' | 'fixed';
    value: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
    targets: PromotionTargetForm[];
}

const props = defineProps<{
    promotion?: Promotion;
    products: PromotionProductOption[];
    categories: OptionItem[];
    brands: OptionItem[];
    colors: Color[];
}>();

const isEdit = !!props.promotion?.id;

const discountTypes = [
    { value: 'percentage', icon: Percent },
    { value: 'fixed', icon: Euro },
] as const;

const targetTabs = [
    { value: 'product', icon: Package, label: 'admin.promotions.tab_products' },
    { value: 'category', icon: LayoutGrid, label: 'admin.promotions.tab_categories' },
    { value: 'brand', icon: Tag, label: 'admin.promotions.tab_brands' },
] as const;

const toDatetimeLocal = (value?: string | null) => (value ? value.slice(0, 16) : '');

const form = useForm<PromotionFormData>({
    name: props.promotion?.name ?? '',
    type: props.promotion?.type ?? 'percentage',
    value: props.promotion?.value ?? 0,
    starts_at: toDatetimeLocal(props.promotion?.starts_at),
    ends_at: toDatetimeLocal(props.promotion?.ends_at),
    is_active: props.promotion?.is_active ?? true,

    // Built from the selections below right before submitting.
    targets: [],
});

const hasErrors = computed(() => Object.keys(form.errors).length > 0);

// Targets are stored as one row per product (+ color), category or brand. The form
// edits them as grouped selections instead, so a large list stays manageable.
// productColors maps a product id to its chosen colors; an empty list means all colors.
const productColors = reactive(new Map<number, number[]>());
const categoryIds = ref<number[]>([]);
const brandIds = ref<number[]>([]);

for (const target of props.promotion?.targets ?? []) {
    if (!target.target_id) continue;
    if (target.type === 'category') categoryIds.value.push(target.target_id);
    else if (target.type === 'brand') brandIds.value.push(target.target_id);
    else {
        const colors = productColors.get(target.target_id);
        // A row without a color covers every color, which wins over specific ones.
        if (target.color_id === null) productColors.set(target.target_id, []);
        else if (!colors) productColors.set(target.target_id, [target.color_id]);
        else if (colors.length) colors.push(target.color_id);
    }
}

const buildTargets = (): PromotionTargetForm[] => [
    ...[...productColors].flatMap(([productId, colors]): PromotionTargetForm[] =>
        (colors.length ? colors : [null]).map((colorId) => ({ type: 'product', target_id: productId, color_id: colorId })),
    ),
    ...categoryIds.value.map((id) => ({ type: 'category' as const, target_id: id, color_id: null })),
    ...brandIds.value.map((id) => ({ type: 'brand' as const, target_id: id, color_id: null })),
];

const targetErrors = computed(() =>
    Object.entries(form.errors as Record<string, string | undefined>)
        .filter(([key]) => key.startsWith('targets.'))
        .map(([, message]) => message)
        .filter((message, index, all) => message && all.indexOf(message) === index),
);

// Summary
const discountLabel = computed(() => {
    const value = Number(form.value) || 0;
    return form.type === 'percentage' ? `-${value}%` : `-${value.toFixed(2)} €`;
});

const state = computed(() => {
    if (!form.is_active) return { key: 'paused', dot: 'bg-gray-400' };
    const now = Date.now();
    if (form.starts_at && new Date(form.starts_at).getTime() > now) return { key: 'scheduled', dot: 'bg-amber-500' };
    if (form.ends_at && new Date(form.ends_at).getTime() < now) return { key: 'expired', dot: 'bg-red-500' };
    if (!form.starts_at && !form.ends_at) return { key: 'always', dot: 'bg-green-500' };
    return { key: 'running', dot: 'bg-green-500' };
});

const formatDate = (value: string) =>
    new Date(value).toLocaleString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });

// Targets
const activeTab = ref<(typeof targetTabs)[number]['value']>(
    !productColors.size && categoryIds.value.length
        ? 'category'
        : !productColors.size && brandIds.value.length
          ? 'brand'
          : 'product',
);

const tabCount = (tab: (typeof targetTabs)[number]['value']) =>
    tab === 'product' ? productColors.size : tab === 'category' ? categoryIds.value.length : brandIds.value.length;

const productsById = computed(() => new Map(props.products.map((p) => [p.id, p])));
const colorsById = computed(() => new Map(props.colors.map((c) => [c.id, c])));

// Products
const pickerOpen = ref(false);
const selectedProductIds = computed(() => new Set(productColors.keys()));

const addProducts = (ids: number[]) => ids.forEach((id) => productColors.set(id, []));
const removeProducts = (ids: number[]) => ids.forEach((id) => productColors.delete(id));

const toggleColor = (productId: number, colorId: number) => {
    const colors = productColors.get(productId)!;
    const index = colors.indexOf(colorId);
    if (index === -1) colors.push(colorId);
    else colors.splice(index, 1);
};

const SELECTED_PAGE = 50;
const selectedSearch = ref('');
const selectedLimit = ref(SELECTED_PAGE);
watch(selectedSearch, () => (selectedLimit.value = SELECTED_PAGE));

const selectedProducts = computed(() => {
    const term = selectedSearch.value.trim().toLowerCase();
    return [...productColors.keys()]
        .map((id) => productsById.value.get(id))
        .filter((p): p is PromotionProductOption => !!p && (!term || p.name.toLowerCase().includes(term)));
});

// Categories & brands
const listSearch = ref('');
watch(activeTab, () => (listSearch.value = ''));

const listOptions = computed(() => {
    const term = listSearch.value.trim().toLowerCase();
    const options = activeTab.value === 'brand' ? props.brands : props.categories;
    return options.filter((o) => !term || o.name.toLowerCase().includes(term));
});

const listSelection = computed(() => (activeTab.value === 'brand' ? brandIds.value : categoryIds.value));

const toggleListItem = (id: number) => {
    const ids = listSelection.value;
    const index = ids.indexOf(id);
    if (index === -1) ids.push(id);
    else ids.splice(index, 1);
};

const submit = () => {
    form.targets = buildTargets();

    const options = { preserveScroll: true };
    if (isEdit) {
        form.put(route('admin.promotions.update', props.promotion!.id), options);
    } else {
        form.post(route('admin.promotions.store'), options);
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto flex w-full max-w-6xl flex-col gap-5 p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <Button variant="outline" size="icon" class="size-9 shrink-0" as-child>
                    <Link :href="route('admin.promotions.index')" :aria-label="t('admin.promotions.back')">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-semibold tracking-tight">
                        {{ isEdit ? promotion!.name : t('admin.promotions.new') }}
                    </h1>
                    <p class="truncate text-sm text-muted-foreground">
                        {{ isEdit ? t('admin.promotions.edit') : t('admin.promotions.subtitle_new') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <Button variant="outline" as-child>
                    <Link :href="route('admin.promotions.index')">{{ t('admin.table.cancel') }}</Link>
                </Button>
                <Button type="submit" :disabled="form.processing" class="cursor-pointer">
                    <Check class="size-4" />
                    {{ isEdit ? t('admin.promotions.save_changes') : t('admin.promotions.save') }}
                </Button>
            </div>
        </div>

        <!-- Error summary -->
        <div
            v-if="hasErrors"
            class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/50 dark:text-red-300"
        >
            <AlertCircle class="size-4 shrink-0" />
            <span>{{ t('admin.promotions.errors_title') }}</span>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <!-- MAIN COLUMN -->
            <div class="space-y-5 lg:col-span-2">
                <!-- Discount details -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.promotions.details') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-1.5">
                            <Label for="name">{{ t('admin.name') }}</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :placeholder="t('admin.promotions.name_placeholder')"
                                :aria-invalid="!!form.errors.name"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label>{{ t('admin.promotions.discount_type') }}</Label>
                                <div class="grid grid-cols-2 gap-2" role="radiogroup">
                                    <button
                                        v-for="discount in discountTypes"
                                        :key="discount.value"
                                        type="button"
                                        role="radio"
                                        :aria-checked="form.type === discount.value"
                                        class="flex h-9 cursor-pointer items-center justify-center gap-1.5 rounded-md border text-sm font-medium transition"
                                        :class="
                                            form.type === discount.value
                                                ? 'border-primary bg-primary text-primary-foreground'
                                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                                        "
                                        @click="form.type = discount.value"
                                    >
                                        <component :is="discount.icon" class="size-3.5" />
                                        {{ t('admin.' + discount.value) }}
                                    </button>
                                </div>
                                <InputError :message="form.errors.type" />
                            </div>

                            <div class="grid gap-1.5">
                                <Label for="value">{{ t('admin.value') }}</Label>
                                <div class="relative">
                                    <Input
                                        id="value"
                                        v-model.number="form.value"
                                        type="number"
                                        min="0"
                                        :max="form.type === 'percentage' ? 100 : undefined"
                                        step="0.01"
                                        class="pr-9 tabular-nums"
                                        :aria-invalid="!!form.errors.value"
                                    />
                                    <span
                                        class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-sm text-muted-foreground"
                                    >
                                        {{ form.type === 'percentage' ? '%' : '€' }}
                                    </span>
                                </div>
                                <p v-if="!form.errors.value" class="text-xs text-muted-foreground">
                                    {{
                                        form.type === 'percentage'
                                            ? t('admin.promotions.percentage_max')
                                            : t('admin.promotions.fixed_min')
                                    }}
                                </p>
                                <InputError :message="form.errors.value" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Schedule -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.promotions.schedule') }}</CardTitle>
                        <CardDescription>{{ t('admin.promotions.schedule_hint') }}</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="starts_at">{{ t('admin.starts_at') }}</Label>
                            <div class="flex gap-1.5">
                                <Input
                                    id="starts_at"
                                    v-model="form.starts_at"
                                    type="datetime-local"
                                    :aria-invalid="!!form.errors.starts_at"
                                />
                                <Button
                                    v-if="form.starts_at"
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    class="size-9 shrink-0 cursor-pointer text-muted-foreground"
                                    :title="t('admin.promotions.clear')"
                                    @click="form.starts_at = ''"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                            <InputError :message="form.errors.starts_at" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="ends_at">{{ t('admin.ends_at') }}</Label>
                            <div class="flex gap-1.5">
                                <Input
                                    id="ends_at"
                                    v-model="form.ends_at"
                                    type="datetime-local"
                                    :min="form.starts_at || undefined"
                                    :aria-invalid="!!form.errors.ends_at"
                                />
                                <Button
                                    v-if="form.ends_at"
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    class="size-9 shrink-0 cursor-pointer text-muted-foreground"
                                    :title="t('admin.promotions.clear')"
                                    @click="form.ends_at = ''"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                            <InputError :message="form.errors.ends_at" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Targets -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.promotions.applies_to') }}</CardTitle>
                        <CardDescription>{{ t('admin.promotions.applies_to_hint') }}</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="inline-flex w-full rounded-md border bg-muted/40 p-0.5 sm:w-auto" role="tablist">
                            <button
                                v-for="tab in targetTabs"
                                :key="tab.value"
                                type="button"
                                role="tab"
                                :aria-selected="activeTab === tab.value"
                                class="flex h-8 flex-1 cursor-pointer items-center justify-center gap-1.5 rounded px-3 text-xs font-medium transition sm:flex-none"
                                :class="
                                    activeTab === tab.value
                                        ? 'bg-background text-foreground shadow-xs'
                                        : 'text-muted-foreground hover:text-foreground'
                                "
                                @click="activeTab = tab.value"
                            >
                                <component :is="tab.icon" class="size-3.5" />
                                {{ t(tab.label) }}
                                <span
                                    v-if="tabCount(tab.value)"
                                    class="rounded-full bg-primary px-1.5 text-[10px] leading-4 text-primary-foreground"
                                >
                                    {{ tabCount(tab.value) }}
                                </span>
                            </button>
                        </div>

                        <!-- Products -->
                        <div v-if="activeTab === 'product'" class="space-y-3">
                            <div
                                v-if="!productColors.size"
                                class="flex flex-col items-center gap-3 rounded-lg border border-dashed px-4 py-8 text-center"
                            >
                                <Package class="size-8 text-muted-foreground" />
                                <p class="text-sm text-muted-foreground">{{ t('admin.promotions.no_products') }}</p>
                                <Button type="button" class="cursor-pointer" @click="pickerOpen = true">
                                    <Plus class="size-4" />
                                    {{ t('admin.promotions.choose_products') }}
                                </Button>
                            </div>

                            <template v-else>
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="relative min-w-40 flex-1">
                                        <Search
                                            class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                                        />
                                        <Input
                                            v-model="selectedSearch"
                                            :placeholder="t('admin.promotions.filter_selected')"
                                            class="pl-8"
                                        />
                                    </div>
                                    <Button type="button" variant="outline" class="cursor-pointer" @click="pickerOpen = true">
                                        <Plus class="size-4" />
                                        {{ t('admin.promotions.choose_products') }}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        class="cursor-pointer text-muted-foreground hover:text-red-600"
                                        @click="productColors.clear()"
                                    >
                                        {{ t('admin.promotions.remove_all') }}
                                    </Button>
                                </div>

                                <p class="text-xs text-muted-foreground">{{ t('admin.promotions.colors_hint') }}</p>

                                <ul class="divide-y rounded-lg border">
                                    <li
                                        v-for="product in selectedProducts.slice(0, selectedLimit)"
                                        :key="product.id"
                                        class="flex items-start gap-3 p-3"
                                    >
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="size-10 shrink-0 rounded border object-cover"
                                            loading="lazy"
                                        />
                                        <div v-else class="flex size-10 shrink-0 items-center justify-center rounded border bg-muted">
                                            <ImageOff class="size-4 text-muted-foreground" />
                                        </div>
                                        <div class="min-w-0 flex-1 space-y-1.5">
                                            <div class="truncate text-sm font-medium">{{ product.name }}</div>
                                            <div class="flex flex-wrap gap-1.5">
                                                <button
                                                    type="button"
                                                    class="h-6 cursor-pointer rounded-full border px-2 text-xs transition"
                                                    :class="
                                                        !productColors.get(product.id)!.length
                                                            ? 'border-primary bg-primary text-primary-foreground'
                                                            : 'text-muted-foreground hover:bg-muted'
                                                    "
                                                    @click="productColors.set(product.id, [])"
                                                >
                                                    {{ t('admin.promotions.all_colors') }}
                                                </button>
                                                <button
                                                    v-for="colorId in product.color_ids"
                                                    :key="colorId"
                                                    type="button"
                                                    class="flex h-6 cursor-pointer items-center gap-1.5 rounded-full border px-2 text-xs transition"
                                                    :class="
                                                        productColors.get(product.id)!.includes(colorId)
                                                            ? 'border-primary bg-primary/10 text-foreground'
                                                            : 'text-muted-foreground hover:bg-muted'
                                                    "
                                                    @click="toggleColor(product.id, colorId)"
                                                >
                                                    <span
                                                        class="size-2.5 rounded-full border"
                                                        :style="{ backgroundColor: colorsById.get(colorId)?.hex_code ?? 'transparent' }"
                                                    />
                                                    {{ colorsById.get(colorId)?.name }}
                                                </button>
                                            </div>
                                        </div>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            class="size-8 shrink-0 cursor-pointer text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                            :title="t('admin.table.delete')"
                                            @click="productColors.delete(product.id)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </li>
                                    <li v-if="!selectedProducts.length" class="p-6 text-center text-sm text-muted-foreground">
                                        {{ t('admin.table.no_results') }}
                                    </li>
                                </ul>

                                <Button
                                    v-if="selectedProducts.length > selectedLimit"
                                    type="button"
                                    variant="outline"
                                    class="w-full cursor-pointer"
                                    @click="selectedLimit += SELECTED_PAGE"
                                >
                                    {{ t('admin.promotions.show_more', { count: selectedProducts.length - selectedLimit }) }}
                                </Button>
                            </template>
                        </div>

                        <!-- Categories / brands -->
                        <div v-else class="space-y-3">
                            <div class="relative">
                                <Search
                                    class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input v-model="listSearch" :placeholder="t('admin.table.search')" class="pl-8" />
                            </div>
                            <div class="max-h-80 overflow-y-auto rounded-lg border">
                                <label
                                    v-for="option in listOptions"
                                    :key="option.id"
                                    class="flex cursor-pointer items-center gap-3 border-b px-3 py-2 text-sm last:border-b-0 hover:bg-muted/50"
                                >
                                    <Checkbox
                                        :model-value="listSelection.includes(option.id)"
                                        @update:model-value="toggleListItem(option.id)"
                                    />
                                    {{ option.name }}
                                </label>
                                <p v-if="!listOptions.length" class="p-6 text-center text-sm text-muted-foreground">
                                    {{ t('admin.table.no_results') }}
                                </p>
                            </div>
                        </div>

                        <InputError :message="form.errors.targets" />
                        <InputError v-for="message in targetErrors" :key="message" :message="message" />
                    </CardContent>
                </Card>

                <PromotionProductPicker
                    v-model:open="pickerOpen"
                    :products="products"
                    :categories="categories"
                    :brands="brands"
                    :selected-ids="selectedProductIds"
                    @add="addProducts"
                    @remove="removeProducts"
                />
            </div>

            <!-- SIDE COLUMN -->
            <div class="space-y-5">
                <!-- Status -->
                <Card class="gap-4 shadow-xs">
                    <CardHeader>
                        <CardTitle>{{ t('admin.promotions.status') }}</CardTitle>
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
                                    {{ t('admin.promotions.active_hint') }}
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

                <!-- Summary -->
                <Card class="gap-4 shadow-xs lg:sticky lg:top-4">
                    <CardHeader>
                        <CardTitle>{{ t('admin.promotions.summary') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="rounded-lg bg-muted/50 p-4 text-center">
                            <div class="text-3xl font-semibold tracking-tight text-red-600 tabular-nums dark:text-red-400">
                                {{ discountLabel }}
                            </div>
                            <div class="mt-1 truncate text-sm text-muted-foreground">
                                {{ form.name || t('admin.promotions.name_placeholder') }}
                            </div>
                        </div>

                        <div class="space-y-2.5 text-sm">
                            <div class="flex items-center gap-2 font-medium">
                                <span class="size-2 rounded-full" :class="state.dot" />
                                {{ t('admin.promotions.state_' + state.key) }}
                            </div>
                            <div class="flex items-start gap-2 text-muted-foreground">
                                <CalendarDays class="mt-0.5 size-4 shrink-0" />
                                <div v-if="form.starts_at || form.ends_at" class="space-y-0.5">
                                    <div v-if="form.starts_at">
                                        {{ t('admin.promotions.from') }} {{ formatDate(form.starts_at) }}
                                    </div>
                                    <div v-if="form.ends_at">
                                        {{ t('admin.promotions.until') }} {{ formatDate(form.ends_at) }}
                                    </div>
                                </div>
                                <span v-else>{{ t('admin.table.no_limit') }}</span>
                            </div>
                            <div class="flex items-start gap-2 text-muted-foreground">
                                <Tag class="mt-0.5 size-4 shrink-0" />
                                <div v-if="productColors.size || categoryIds.length || brandIds.length" class="space-y-0.5">
                                    <div v-if="productColors.size">
                                        {{ t('admin.promotions.summary_products', { count: productColors.size }) }}
                                    </div>
                                    <div v-if="categoryIds.length">
                                        {{ t('admin.promotions.summary_categories', { count: categoryIds.length }) }}
                                    </div>
                                    <div v-if="brandIds.length">
                                        {{ t('admin.promotions.summary_brands', { count: brandIds.length }) }}
                                    </div>
                                </div>
                                <span v-else>{{ t('admin.promotions.summary_nothing') }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Footer actions -->
        <div class="flex justify-end gap-2 border-t pt-5">
            <Button variant="outline" as-child>
                <Link :href="route('admin.promotions.index')">{{ t('admin.table.cancel') }}</Link>
            </Button>
            <Button type="submit" :disabled="form.processing" class="cursor-pointer">
                <Check class="size-4" />
                {{ isEdit ? t('admin.promotions.save_changes') : t('admin.promotions.save') }}
            </Button>
        </div>
    </form>
</template>
