<script setup lang="ts">
import { defineProps, reactive, watch } from 'vue';
import { type Filters, MenuType, MenuItem } from '@/types';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const props = defineProps<{
    filterOptions: MenuType;
    filters: Filters;
}>();

const form = reactive<Filters>({
    skin_types: [...(props.filters.skin_types ?? [])],
    skin_concerns: [...(props.filters.skin_concerns ?? [])],
    product_types: [...(props.filters.product_types ?? [])],
    extras: [...(props.filters.extras ?? [])],
    order_by: props.filters.order_by ?? null,
    per_page: props.filters.per_page ?? null,
    search: props.filters.search ?? null,
});

const openGroups = reactive<Record<keyof Filters, boolean>>({
    skin_types: true,
    skin_concerns: true,
    product_types: true,
    extras: true,
    per_page: true,
    order_by: true,
    search:true,
});

type FilterGroupKey = keyof Filters;

interface FilterGroup {
    key: FilterGroupKey;
    title: string;
    items: MenuItem[];
}

const filterGroups: FilterGroup[] = [
    {
        key: 'skin_types',
        title: t('home.skin_types'),
        items: props.filterOptions.skinTypes.data,
    },
    {
        key: 'skin_concerns',
        title: t('home.skin_concerns'),
        items: props.filterOptions.skinConcerns.data,
    },
    {
        key: 'product_types',
        title: t('home.product_types'),
        items: props.filterOptions.productTypes.data,
    },
    {
        key: 'extras',
        title: t('home.extra'),
        items: props.filterOptions.extras.data,
    },
];

const emit = defineEmits<{
    (e: 'update:filters', value: Partial<Filters>): void;
}>();

watch(
    form,
    () => {
        emit('update:filters', {
            skin_types: [...form.skin_types],
            skin_concerns: [...form.skin_concerns],
            product_types: [...form.product_types],
            extras: [...form.extras],
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
