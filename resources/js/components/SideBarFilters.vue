<script setup lang="ts">
import { reactive, watch } from 'vue';
import { type Filters, MenuType, MenuItem } from '@/types';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
const { t } = useI18n();

const props = defineProps<{
    filterOptions: MenuType;
    filters: Filters;
}>();

const form = reactive<Filters>({
    categories: [...(props.filters.categories ?? [])],
    brands: [...(props.filters.brands ?? [])],
    seasons: [...(props.filters.seasons ?? [])],
    colors: [...(props.filters.colors ?? [])],
    sizes: [...(props.filters.sizes ?? [])],
    gender: props.filters.gender ?? null,
    order_by: props.filters.order_by ?? null,
    per_page: props.filters.per_page ?? null,
    search: props.filters.search ?? null,
    on_sale: !!props.filters.on_sale,
});

// The sale collection always filters by on_sale, so the toggle would do nothing there.
const showSaleToggle = !route().current('collection.sale');

type CheckboxGroupKey = 'categories' | 'brands' | 'seasons' | 'colors' | 'sizes';

const openGroups = reactive<Record<CheckboxGroupKey | 'gender', boolean>>({
    categories: true,
    brands: true,
    seasons: true,
    colors: true,
    sizes: true,
    gender: true,
});

interface FilterGroup {
    key: CheckboxGroupKey;
    title: string;
    items: MenuItem[];
}

const filterGroups: FilterGroup[] = [
    {
        key: 'categories',
        title: t('home.categories'),
        items: props.filterOptions.categories.data,
    },
    {
        key: 'brands',
        title: t('home.marks'),
        items: props.filterOptions.brands.data,
    },
    {
        key: 'seasons',
        title: t('home.seasons'),
        items: props.filterOptions.seasons.data,
    },
    {
        key: 'colors',
        title: t('home.colors'),
        items: props.filterOptions.colors.data,
    },
    {
        key: 'sizes',
        title: t('home.sizes'),
        items: props.filterOptions.sizes.data,
    },
];

const genderOptions = [
    { value: 'boy', label: t('home.boy') },
    { value: 'girl', label: t('home.girl') },
    { value: 'unisex', label: t('home.unisex') },
];

const emit = defineEmits<{
    (e: 'update:filters', value: Partial<Filters>): void;
}>();

watch(
    form,
    () => {
        emit('update:filters', {
            categories: [...form.categories],
            brands: [...form.brands],
            seasons: [...form.seasons],
            colors: [...form.colors],
            sizes: [...form.sizes],
            gender: form.gender,
            on_sale: form.on_sale,
        });
    },
    { deep: true },
);
</script>

<template>
    <aside class="h-fit w-full shrink-0 lg:sticky lg:top-24">
        <label
            v-if="showSaleToggle"
            class="mb-5 flex cursor-pointer items-center gap-2.5 border-b border-border pb-3 text-sm font-semibold"
        >
            <input
                type="checkbox"
                v-model="form.on_sale"
                class="h-4 w-4 cursor-pointer rounded border-input accent-red-600"
            />
            <span class="text-red-600">{{ t('home.on_sale') }}</span>
        </label>
        <div v-for="group in filterGroups" :key="group.key">
            <div v-if="group.items.length" class="mb-5">
                <button
                    @click="openGroups[group.key] = !openGroups[group.key]"
                    class="mb-3 flex w-full items-center justify-between border-b border-border pb-2 text-sm font-semibold"
                >
                    {{ group.title }}
                    <component
                        :is="openGroups[group.key] ? ChevronUp : ChevronDown"
                        class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                    />
                </button>

                <div v-show="openGroups[group.key]" class="space-y-2.5">
                    <label
                        v-for="item in group.items"
                        :key="item.id"
                        class="flex cursor-pointer items-center gap-2.5 text-muted-foreground transition-colors hover:text-foreground"
                    >
                        <input
                            type="checkbox"
                            :value="item.id"
                            v-model="form[group.key]"
                            class="h-4 w-4 cursor-pointer rounded border-input accent-foreground"
                        />
                        <span class="text-sm">{{ item.name }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div>
            <button
                @click="openGroups.gender = !openGroups.gender"
                class="mb-3 flex w-full items-center justify-between border-b border-border pb-2 text-sm font-semibold"
            >
                {{ t('home.gender') }}
                <component
                    :is="openGroups.gender ? ChevronUp : ChevronDown"
                    class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                />
            </button>

            <div v-show="openGroups.gender" class="space-y-2.5">
                <label
                    v-for="option in genderOptions"
                    :key="option.value"
                    class="flex cursor-pointer items-center gap-2.5 text-muted-foreground transition-colors hover:text-foreground"
                >
                    <input
                        type="radio"
                        name="gender"
                        :value="option.value"
                        v-model="form.gender"
                        class="h-4 w-4 cursor-pointer border-input accent-foreground"
                    />
                    <span class="text-sm">{{ option.label }}</span>
                </label>
            </div>
        </div>
    </aside>
</template>

<style scoped>
@media (min-width: 1024px) {
    aside {
        width: 220px;
    }
}
</style>
