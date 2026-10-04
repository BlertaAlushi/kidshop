<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ImageOff, Search } from 'lucide-vue-next';
import { PromotionProductOption } from '@/types';

interface OptionItem {
    id: number;
    name: string;
}

const props = defineProps<{
    products: PromotionProductOption[];
    categories: OptionItem[];
    brands: OptionItem[];
    selectedIds: Set<number>;
}>();

const open = defineModel<boolean>('open', { required: true });

const emit = defineEmits<{
    add: [ids: number[]];
    remove: [ids: number[]];
}>();

const { t } = useI18n();

// Rendering thousands of rows makes the dialog sluggish; search narrows the rest.
const RENDER_LIMIT = 100;
const ALL = 'all';

const search = ref('');
const categoryId = ref<number | typeof ALL>(ALL);
const brandId = ref<number | typeof ALL>(ALL);

watch(open, (isOpen) => {
    if (isOpen) {
        search.value = '';
        categoryId.value = ALL;
        brandId.value = ALL;
    }
});

const categoryNames = computed(() => new Map(props.categories.map((c) => [c.id, c.name])));
const brandNames = computed(() => new Map(props.brands.map((b) => [b.id, b.name])));

const filtered = computed(() => {
    const term = search.value.trim().toLowerCase();
    return props.products.filter(
        (product) =>
            (!term || product.name.toLowerCase().includes(term)) &&
            (categoryId.value === ALL || product.category_id === categoryId.value) &&
            (brandId.value === ALL || product.brand_id === brandId.value),
    );
});

const visible = computed(() => filtered.value.slice(0, RENDER_LIMIT));

const selectedInFiltered = computed(() => filtered.value.filter((p) => props.selectedIds.has(p.id)).length);

const allState = computed<boolean | 'indeterminate'>(() => {
    if (!filtered.value.length || selectedInFiltered.value === 0) return false;
    return selectedInFiltered.value === filtered.value.length ? true : 'indeterminate';
});

const toggleAll = () => {
    const ids = filtered.value.map((p) => p.id);
    if (allState.value === true) emit('remove', ids);
    else emit('add', ids.filter((id) => !props.selectedIds.has(id)));
};

const toggle = (id: number) => (props.selectedIds.has(id) ? emit('remove', [id]) : emit('add', [id]));
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="flex max-h-[90vh] flex-col gap-4 sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ t('admin.promotions.picker_title') }}</DialogTitle>
                <DialogDescription>{{ t('admin.promotions.picker_hint') }}</DialogDescription>
            </DialogHeader>

            <div class="grid gap-2 sm:grid-cols-[1fr_auto_auto]">
                <div class="relative">
                    <Search class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" :placeholder="t('admin.promotions.picker_search')" class="pl-8" autofocus />
                </div>
                <Select v-model="categoryId">
                    <SelectTrigger class="w-full sm:w-40">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">{{ t('admin.promotions.all_categories') }}</SelectItem>
                        <SelectItem v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <Select v-model="brandId">
                    <SelectTrigger class="w-full sm:w-40">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem :value="ALL">{{ t('admin.promotions.all_brands') }}</SelectItem>
                        <SelectItem v-for="brand in brands" :key="brand.id" :value="brand.id">
                            {{ brand.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <label
                v-if="filtered.length"
                class="flex cursor-pointer items-center gap-3 rounded-md border bg-muted/40 px-3 py-2 text-sm font-medium"
            >
                <Checkbox :model-value="allState" @update:model-value="toggleAll" />
                {{ t('admin.promotions.picker_select_all', { count: filtered.length }) }}
                <span class="ml-auto text-xs font-normal text-muted-foreground">
                    {{ t('admin.promotions.picker_selected', { count: selectedIds.size }) }}
                </span>
            </label>

            <div class="-mx-1 min-h-0 flex-1 overflow-y-auto px-1">
                <p v-if="!filtered.length" class="py-10 text-center text-sm text-muted-foreground">
                    {{ t('admin.table.no_results') }}
                </p>
                <ul v-else class="divide-y rounded-md border">
                    <li v-for="product in visible" :key="product.id">
                        <label class="flex cursor-pointer items-center gap-3 px-3 py-2 hover:bg-muted/50">
                            <Checkbox :model-value="selectedIds.has(product.id)" @update:model-value="toggle(product.id)" />
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
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-medium">{{ product.name }}</div>
                                <div class="truncate text-xs text-muted-foreground">
                                    {{ categoryNames.get(product.category_id!) ?? '—' }}
                                    · {{ brandNames.get(product.brand_id!) ?? '—' }}
                                </div>
                            </div>
                            <span
                                v-if="!product.is_active"
                                class="shrink-0 rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                            >
                                {{ t('admin.table.inactive') }}
                            </span>
                        </label>
                    </li>
                </ul>
                <p v-if="filtered.length > RENDER_LIMIT" class="pt-3 text-center text-xs text-muted-foreground">
                    {{ t('admin.promotions.picker_more', { shown: RENDER_LIMIT, total: filtered.length }) }}
                </p>
            </div>

            <DialogFooter>
                <Button type="button" class="cursor-pointer" @click="open = false">
                    {{ t('admin.promotions.picker_done') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
