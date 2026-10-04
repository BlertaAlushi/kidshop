<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SideBarFilters from '@/components/SideBarFilters.vue';
import { type Filters, ProductVariantListItem, type PageType } from '@/types';
import ProductCard from '@/components/ProductCard.vue';
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Ellipsis, Search, SlidersHorizontal } from 'lucide-vue-next';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ItemGroup } from '@/components/ui/item';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';

import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '@/components/ui/empty';

import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import SearchOverlay from '@/components/SearchOverlay.vue';
import { route } from 'ziggy-js';

const page = usePage<PageType>();

const { t } = useI18n();

const props = defineProps<{
    products: {
        data: ProductVariantListItem[];
        links: any;
        meta: {
            current_page: number;
            last_page: number;
            total: number;
            links: any[];
        };
    };
    filters: Filters;
}>();

const products = computed(() => props.products);

const filterOptions = page.props.menu;

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

const isSaleCollection = route().current('collection.sale');

watch(
    form,
    () => {
        const query: Record<string, any> = {};

        Object.entries(form).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                if (value.length > 0) query[key] = value;
            } else if (value != null && value !== '' && value !== false) {
                query[key] = value;
            }
        });

        router.get(window.location.pathname, query, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
        });
    },
    { deep: true },
);

const searchOpen = ref(!!props.filters.search?.length);

function toggleSearch() {
    searchOpen.value = !searchOpen.value;
}

const mobileFiltersOpen = ref(false);

const activeFilterCount = computed(
    () =>
        form.categories.length +
        form.brands.length +
        form.seasons.length +
        form.colors.length +
        form.sizes.length +
        (form.gender ? 1 : 0) +
        (form.on_sale && !isSaleCollection ? 1 : 0),
);
</script>

<template>
    <Head title="Products" />
    <AppLayout>
        <div
            class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-6 py-10 lg:flex-row lg:gap-x-10"
        >
            <div class="hidden lg:block">
                <SideBarFilters
                    :filters="form"
                    :filterOptions="filterOptions"
                    @update:filters="(update) => Object.assign(form, update)"
                />
            </div>
            <main class="flex flex-1 flex-col gap-y-8">
                <div
                    class="flex flex-wrap items-center justify-between gap-3"
                >
                    <h1 class="text-xl font-semibold tracking-tight">
                        {{ form.on_sale ? t('home.sale') : t('home.products') }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="lg:hidden">
                            <Sheet v-model:open="mobileFiltersOpen">
                                <SheetTrigger as-child>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="cursor-pointer gap-2 rounded-full"
                                    >
                                        <SlidersHorizontal class="size-4" />
                                        {{ t('home.filters') }}
                                        <span
                                            v-if="activeFilterCount"
                                            class="flex h-4.5 min-w-4.5 items-center justify-center rounded-full bg-foreground px-1 text-[10px] font-bold text-background"
                                        >
                                            {{ activeFilterCount }}
                                        </span>
                                    </Button>
                                </SheetTrigger>
                                <SheetContent
                                    side="left"
                                    class="w-80 overflow-y-auto p-6"
                                >
                                    <SheetHeader class="p-0">
                                        <SheetTitle>{{
                                            t('home.filters')
                                        }}</SheetTitle>
                                    </SheetHeader>
                                    <SideBarFilters
                                        :filters="form"
                                        :filterOptions="filterOptions"
                                        @update:filters="
                                            (update) =>
                                                Object.assign(form, update)
                                        "
                                    />
                                </SheetContent>
                            </Sheet>
                        </div>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer rounded-full"
                            @click="toggleSearch"
                        >
                            <Search
                                class="size-5 opacity-80 group-hover:opacity-100"
                            />
                        </Button>
                        <Select v-model="form.order_by">
                            <SelectTrigger class="w-45">
                                <SelectValue :placeholder="t('home.order_by')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>{{
                                        t('home.order_by')
                                    }}</SelectLabel>
                                    <SelectItem value="availability">
                                        {{ t('home.availability') }}
                                    </SelectItem>
                                    <SelectItem value="price_high_to_low">
                                        {{ t('home.price_high_to_low') }}
                                    </SelectItem>
                                    <SelectItem value="price_low_to_high">
                                        {{ t('home.price_low_to_high') }}
                                    </SelectItem>
                                    <SelectItem value="date_new_to_old">
                                        {{ t('home.date_new_to_old') }}
                                    </SelectItem>
                                    <SelectItem value="date_old_to_new">
                                        {{ t('home.date_old_to_new') }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                        <Select v-model="form.per_page">
                            <SelectTrigger class="w-32">
                                <SelectValue
                                    :placeholder="t('home.show_per_page')"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>{{
                                        t('home.show_per_page')
                                    }}</SelectLabel>
                                    <SelectItem value="12"> 12 </SelectItem>
                                    <SelectItem value="24"> 24 </SelectItem>
                                    <SelectItem value="48"> 48 </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
                <SearchOverlay
                    v-if="searchOpen"
                    :filters="form"
                    @update:filters="(update) => Object.assign(form, update)"
                />
                <div v-if="products.data.length" class="flex flex-col gap-y-10">
                    <div class="flex flex-col gap-6">
                        <ItemGroup
                            class="grid grid-cols-2 gap-x-5 gap-y-10 sm:grid-cols-3"
                        >
                            <ProductCard
                                v-for="item in products.data"
                                :key="item.id"
                                :item="item"
                            />
                        </ItemGroup>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <Button
                            size="sm"
                            variant="outline"
                            class="rounded-full"
                            :disabled="!products.links.prev"
                            @click="
                                products.links.prev &&
                                router.get(products.links.prev)
                            "
                        >
                            {{ t('home.previous') }}
                        </Button>

                        <Button
                            v-if="products.meta.current_page !== 1"
                            size="sm"
                            variant="outline"
                            class="rounded-full"
                            @click="
                                products.links.first &&
                                router.get(products.links.first)
                            "
                        >
                            1
                        </Button>

                        <Ellipsis
                            v-if="products.meta.current_page > 2"
                            class="size-4 text-muted-foreground"
                        />

                        <Button
                            size="sm"
                            class="rounded-full"
                            @click="
                                products.meta.links[products.meta.current_page]
                                    .url &&
                                router.get(
                                    products.meta.links[
                                        products.meta.current_page
                                    ].url,
                                )
                            "
                        >
                            {{ products.meta.current_page }}
                        </Button>

                        <Ellipsis
                            v-if="
                                products.meta.current_page <
                                products.meta.last_page - 1
                            "
                            class="size-4 text-muted-foreground"
                        />

                        <Button
                            v-if="
                                products.meta.current_page !==
                                products.meta.last_page
                            "
                            size="sm"
                            variant="outline"
                            class="rounded-full"
                            @click="
                                products.links.last &&
                                router.get(products.links.last)
                            "
                        >
                            {{ products.meta.last_page }}
                        </Button>

                        <Button
                            size="sm"
                            variant="outline"
                            class="rounded-full"
                            :disabled="!products.links.next"
                            @click="
                                products.links.next &&
                                router.get(products.links.next)
                            "
                        >
                            {{ t('home.next') }}
                        </Button>
                    </div>
                </div>
                <div v-else>
                    <Empty
                        class="rounded-2xl border border-border/70 bg-card/50 py-16"
                    >
                        <EmptyHeader>
                            <EmptyTitle>{{ t('home.no_products') }}</EmptyTitle>
                            <EmptyDescription>
                                {{ t('home.no_products_description') }}
                            </EmptyDescription>
                        </EmptyHeader>
                    </Empty>
                </div>
            </main>
        </div>
    </AppLayout>
</template>

<style scoped></style>
