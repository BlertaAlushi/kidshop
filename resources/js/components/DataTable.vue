<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import {
    ArrowDown,
    ArrowUp,
    ArrowUpDown,
    CheckCircle,
    ChevronLeft,
    ChevronRight,
    Inbox,
    Pencil,
    Plus,
    Search,
    Trash2,
    X,
} from 'lucide-vue-next';

import {
    ColumnDef,
    FlexRender,
    SortingState,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { PageType } from '@/types';

const { t } = useI18n();
const page = usePage<PageType>();

const props = defineProps<{
    table_rows: Record<string, any>[]; // any model
    columns: ColumnDef<any>[];
    page_name: string;
    title?: string;
}>();

// Flash message
const message = computed(() => page.props.flash.success);
let flashTimeout: ReturnType<typeof setTimeout> | undefined;
const dismissFlash = () => {
    clearTimeout(flashTimeout);
    page.props.flash.success = null;
};
watch(
    message,
    (val) => {
        if (val) {
            clearTimeout(flashTimeout);
            flashTimeout = setTimeout(dismissFlash, 3000);
        }
    },
    { immediate: true },
);

// Table
const globalFilter = ref('');
const sorting = ref<SortingState>([]);
const pageSizes = [10, 20, 50];

const table = useVueTable({
    get data() {
        return props.table_rows;
    },
    get columns() {
        return props.columns;
    },
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
        get sorting() {
            return sorting.value;
        },
    },
    onGlobalFilterChange: (value) => {
        globalFilter.value = String(value ?? '');
    },
    onSortingChange: (updater) => {
        sorting.value = typeof updater === 'function' ? updater(sorting.value) : updater;
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    initialState: { pagination: { pageSize: 10 } },
});

const totalRows = computed(() => props.table_rows.length);
const filteredCount = computed(() => table.getFilteredRowModel().rows.length);
const pagination = computed(() => table.getState().pagination);
const rangeFrom = computed(() =>
    filteredCount.value === 0 ? 0 : pagination.value.pageIndex * pagination.value.pageSize + 1,
);
const rangeTo = computed(() =>
    Math.min((pagination.value.pageIndex + 1) * pagination.value.pageSize, filteredCount.value),
);

const pageSize = computed({
    get: () => String(pagination.value.pageSize),
    set: (value: string) => table.setPageSize(Number(value)),
});

const clearSearch = () => table.setGlobalFilter('');

// Row keys: slug-based routes use the slug, countries use iso_2 as primary key
const editKey = (row: Record<string, any>) => row.slug ?? row.iso_2 ?? row.id;
const deleteKey = (row: Record<string, any>) => row.id ?? row.iso_2;
const rowLabel = (row: Record<string, any>) => row.name ?? row.country ?? row.iso_2 ?? `#${row.id}`;

const createNew = () => router.get(route('admin.' + props.page_name + '.create'));
const editRow = (row: Record<string, any>) =>
    router.get(route('admin.' + props.page_name + '.edit', editKey(row)));

// Delete confirmation
const pendingDelete = ref<Record<string, any> | null>(null);
const deleting = ref(false);
const deleteOpen = computed({
    get: () => pendingDelete.value !== null,
    set: (open: boolean) => {
        if (!open && !deleting.value) pendingDelete.value = null;
    },
});

const confirmDelete = () => {
    if (!pendingDelete.value) return;
    deleting.value = true;
    router.delete(route('admin.' + props.page_name + '.destroy', deleteKey(pendingDelete.value)), {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            pendingDelete.value = null;
        },
    });
};
</script>

<template>
    <div class="flex flex-col gap-5 p-4 md:p-10">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h1 v-if="title" class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
                <span class="rounded-full bg-muted px-2.5 py-0.5 text-sm text-muted-foreground">
                    {{ totalRows }}
                </span>
            </div>
            <Button class="cursor-pointer" @click="createNew">
                <Plus class="size-4" />
                {{ t('admin.' + page_name + '.new') }}
            </Button>
        </div>

        <!-- Flash -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="-translate-y-1 opacity-0"
            leave-active-class="transition duration-200"
            leave-to-class="-translate-y-1 opacity-0"
        >
            <div
                v-if="message"
                class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900 dark:bg-green-950/50 dark:text-green-300"
            >
                <CheckCircle class="size-4 shrink-0" />
                <span class="flex-1">{{ t('admin.' + page_name + '.' + message) }}</span>
                <button type="button" class="cursor-pointer opacity-70 hover:opacity-100" @click="dismissFlash">
                    <X class="size-4" />
                </button>
            </div>
        </Transition>

        <!-- Card -->
        <div class="overflow-hidden rounded-xl border bg-card shadow-xs">
            <!-- Toolbar -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b p-3">
                <div class="relative w-full sm:w-72">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        :model-value="globalFilter"
                        :placeholder="t('admin.table.search')"
                        class="px-8"
                        @update:model-value="table.setGlobalFilter($event)"
                    />
                    <button
                        v-if="globalFilter"
                        type="button"
                        class="absolute top-1/2 right-2.5 -translate-y-1/2 cursor-pointer text-muted-foreground hover:text-foreground"
                        @click="clearSearch"
                    >
                        <X class="size-4" />
                    </button>
                </div>
                <div v-if="globalFilter" class="text-sm text-muted-foreground">
                    {{ t('admin.table.results', { count: filteredCount }) }}
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <Table>
                    <TableHeader class="bg-muted/40">
                        <TableRow class="hover:bg-transparent">
                            <TableHead
                                v-for="header in table.getHeaderGroups()[0].headers"
                                :key="header.id"
                                class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                <button
                                    v-if="header.column.getCanSort()"
                                    type="button"
                                    class="-ml-1 inline-flex cursor-pointer items-center gap-1 rounded px-1 py-0.5 hover:text-foreground"
                                    @click="header.column.toggleSorting()"
                                >
                                    <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                                    <ArrowUp v-if="header.column.getIsSorted() === 'asc'" class="size-3.5" />
                                    <ArrowDown v-else-if="header.column.getIsSorted() === 'desc'" class="size-3.5" />
                                    <ArrowUpDown v-else class="size-3.5 opacity-40" />
                                </button>
                                <FlexRender v-else :render="header.column.columnDef.header" :props="header.getContext()" />
                            </TableHead>
                            <TableHead class="w-24 text-right text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                {{ t('admin.table.actions') }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <TableRow
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            class="group cursor-pointer"
                            @click="editRow(row.original)"
                        >
                            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" class="py-3">
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </TableCell>
                            <TableCell class="py-3 text-right whitespace-nowrap">
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    class="size-8 cursor-pointer text-muted-foreground hover:text-foreground"
                                    :title="t('admin.table.edit')"
                                    @click.stop="editRow(row.original)"
                                >
                                    <Pencil class="size-4" />
                                </Button>
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    class="size-8 cursor-pointer text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40"
                                    :title="t('admin.table.delete')"
                                    @click.stop="pendingDelete = row.original"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </TableCell>
                        </TableRow>

                        <!-- Empty state -->
                        <TableRow v-if="table.getRowModel().rows.length === 0" class="hover:bg-transparent">
                            <TableCell :colspan="columns.length + 1" class="py-14">
                                <div class="flex flex-col items-center gap-3 text-center">
                                    <div class="flex size-12 items-center justify-center rounded-full bg-muted">
                                        <component :is="globalFilter ? Search : Inbox" class="size-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="font-medium">
                                            {{ globalFilter ? t('admin.table.no_results') : t('admin.table.empty') }}
                                        </p>
                                        <p class="text-sm text-muted-foreground">
                                            {{ globalFilter ? t('admin.table.no_results_hint') : t('admin.table.empty_hint') }}
                                        </p>
                                    </div>
                                    <Button v-if="globalFilter" variant="outline" size="sm" class="cursor-pointer" @click="clearSearch">
                                        {{ t('admin.table.clear_search') }}
                                    </Button>
                                    <Button v-else size="sm" class="cursor-pointer" @click="createNew">
                                        <Plus class="size-4" />
                                        {{ t('admin.' + page_name + '.new') }}
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div
                v-if="filteredCount > 0"
                class="flex flex-wrap items-center justify-between gap-3 border-t px-3 py-2.5 text-sm text-muted-foreground"
            >
                <div class="flex items-center gap-2">
                    <span>{{ t('admin.table.rows_per_page') }}</span>
                    <Select v-model="pageSize">
                        <SelectTrigger size="sm" class="w-18">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="size in pageSizes" :key="size" :value="String(size)">
                                {{ size }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex items-center gap-3">
                    <span>{{ t('admin.table.showing', { from: rangeFrom, to: rangeTo, total: filteredCount }) }}</span>
                    <div class="flex gap-1">
                        <Button
                            size="icon"
                            variant="outline"
                            class="size-8 cursor-pointer"
                            :disabled="!table.getCanPreviousPage()"
                            :title="t('home.previous')"
                            @click="table.previousPage()"
                        >
                            <ChevronLeft class="size-4" />
                        </Button>
                        <Button
                            size="icon"
                            variant="outline"
                            class="size-8 cursor-pointer"
                            :disabled="!table.getCanNextPage()"
                            :title="t('home.next')"
                            @click="table.nextPage()"
                        >
                            <ChevronRight class="size-4" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete confirmation -->
        <Dialog v-model:open="deleteOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ t('admin.table.delete_title') }}</DialogTitle>
                    <DialogDescription>
                        {{ t('admin.table.delete_description', { name: pendingDelete ? rowLabel(pendingDelete) : '' }) }}
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <Button variant="outline" class="cursor-pointer" :disabled="deleting" @click="deleteOpen = false">
                        {{ t('admin.table.cancel') }}
                    </Button>
                    <Button variant="destructive" class="cursor-pointer" :disabled="deleting" @click="confirmDelete">
                        <Trash2 class="size-4" />
                        {{ t('admin.table.delete') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
