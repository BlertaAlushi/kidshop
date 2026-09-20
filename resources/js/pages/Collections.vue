<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SideBarFilters from '@/components/SideBarFilters.vue';
import { type Filters, Product, type PageType } from '@/types';
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Ellipsis, Search } from 'lucide-vue-next';
import { route } from 'ziggy-js'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Item,
    ItemContent,
    ItemDescription,
    ItemGroup,
    ItemHeader,
    ItemTitle,
} from '@/components/ui/item';

import {
    Empty,
    EmptyDescription,
    EmptyHeader,
    EmptyTitle,
} from '@/components/ui/empty';

import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import SearchOverlay from '@/components/SearchOverlay.vue';

const page = usePage<PageType>();

const { t } = useI18n();

const props = defineProps<{
    products: {
        data: Product[];
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
    skin_types: [...(props.filters.skin_types ?? [])],
    skin_concerns: [...(props.filters.skin_concerns ?? [])],
    product_types: [...(props.filters.product_types ?? [])],
    extras: [...(props.filters.extras ?? [])],
    order_by: props.filters.order_by ?? null,
    per_page: props.filters.per_page ?? null,
    search: props.filters.search ?? null,
});

watch(
    form,
    () => {
        const query: Record<string, any> = {};

        Object.entries(form).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                if (value.length > 0) query[key] = value;
            } else if (value != null && value !== '') {
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
</script>

<template>
    <Head title="Products" />
    <AppLayout>
        <div class="flex gap-x-14 p-8">
            <SideBarFilters
                :filters="form"
                :filterOptions="filterOptions"
                @update:filters="(update) => Object.assign(form, update)"
            />
            <main class="flex flex-1 flex-col gap-y-10">
                <div class="ml-auto flex gap-3">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-9 w-9 cursor-pointer"
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
                        <SelectTrigger class="w-45">
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
                <SearchOverlay
                    v-if="searchOpen"
                    :filters="form"
                    @update:filters="(update) => Object.assign(form, update)"
                />
                <div v-if="products.data.length" class="flex flex-col gap-y-10">
                    <div class="flex flex-col gap-6">
                        <ItemGroup class="grid grid-cols-4 gap-6">
                            <Item
                                v-for="product in products.data"
                                :key="product.name"
                                variant="outline"
                                as-child
                                role="listitem"
                            >
                                <a :href="route('collection.product',product.slug)">
                                    <ItemHeader>
                                        <img
                                            :src="product.image"
                                            :alt="product.name"
                                            width="128"
                                            height="128"
                                            class="aspect-square w-full rounded-sm object-cover"
                                        />
                                    </ItemHeader>
                                    <ItemContent>
                                        <ItemTitle>{{
                                            product.name
                                        }}</ItemTitle>
                                        <ItemDescription
                                            >{{ product.price }}
                                            {{ product.currency }}
                                        </ItemDescription>
                                    </ItemContent>
                                </a>
                            </Item>
                        </ItemGroup>
                    </div>
                    <div class="ml-auto flex gap-2">
                        <Button
                            size="sm"
                            variant="outline"
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
                            @click="
                                products.links.first &&
                                router.get(products.links.first)
                            "
                        >
                            1
                        </Button>

                        <Ellipsis v-if="products.meta.current_page > 2" />

                        <Button
                            size="sm"
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
                        />

                        <Button
                            v-if="
                                products.meta.current_page !==
                                products.meta.last_page
                            "
                            size="sm"
                            variant="outline"
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
                        class="h-full bg-linear-to-b from-muted/50 from-30% to-background"
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
