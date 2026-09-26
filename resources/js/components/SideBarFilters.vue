<script setup lang="ts">
import { reactive, watch } from 'vue';
import { type Filters, MenuType, MenuItem } from '@/types';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
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
});

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
        });
    },
    { deep: true },
);
</script>

<template>
    <aside class="sticky h-screen shrink-0 overflow-y-auto">
        <div v-for="group in filterGroups" :key="group.key">
            <div v-if="group.items.length" class="mb-4">
                <button
                    @click="openGroups[group.key] = !openGroups[group.key]"
                    :class="[
                        'mb-3 flex w-full items-center justify-between pb-2 font-medium',
                        openGroups[group.key] ? 'border-b border-gray-900' : '',
                    ]"
                >
                    {{ group.title }}
                    <component
                        :is="openGroups[group.key] ? ChevronUp : ChevronDown"
                        class="h-4 w-4 transition-transform duration-200"
                    />
                </button>

                <div v-show="openGroups[group.key]" class="space-y-2 pl-2">
                    <label
                        v-for="item in group.items"
                        :key="item.id"
                        class="flex cursor-pointer items-center gap-2"
                    >
                        <input
                            type="checkbox"
                            :value="item.id"
                            v-model="form[group.key]"
                            class="h-4 w-4 accent-black"
                        />
                        <span class="text-sm">{{ item.name }}</span>
                    </label>
                </div>
            </div>
        </div>

        <div>
            <button
                @click="openGroups.gender = !openGroups.gender"
                :class="[
                    'mb-3 flex w-full items-center justify-between pb-2 font-medium',
                    openGroups.gender ? 'border-b border-gray-900' : '',
                ]"
            >
                {{ t('home.gender') }}
                <component
                    :is="openGroups.gender ? ChevronUp : ChevronDown"
                    class="h-4 w-4 transition-transform duration-200"
                />
            </button>

            <div v-show="openGroups.gender" class="space-y-2 pl-2">
                <label
                    v-for="option in genderOptions"
                    :key="option.value"
                    class="flex cursor-pointer items-center gap-2"
                >
                    <input
                        type="radio"
                        name="gender"
                        :value="option.value"
                        v-model="form.gender"
                        class="h-4 w-4 accent-black"
                    />
                    <span class="text-sm">{{ option.label }}</span>
                </label>
            </div>
        </div>
    </aside>
</template>

<style scoped>
aside {
    min-width: 250px;
}
button {
    cursor: pointer;
    transition: color 0.2s;
}
label input {
    cursor: pointer;
}
</style>
