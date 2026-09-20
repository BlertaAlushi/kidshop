<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';
import { Trash, Pencil, CheckCircle } from 'lucide-vue-next';

import {
    ColumnDef,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
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
import { Alert, AlertTitle } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { PageType } from '@/types';

const { t } = useI18n();
const page = usePage<PageType>();

const showAlert = computed(() => !!page.props.flash.success);
const message = computed(() => page.props.flash.success);
watch(
    () => page.props.flash.success,
    (val) => {
        if (val) {
            setTimeout(() => {
                page.props.flash.success = null;
            }, 3000);
        }
    },
    { immediate: true },
);

const props = defineProps<{
    table_rows: Record<string, any>[]; // any model
    columns: ColumnDef<any>[];
    page_name: string;
}>();

const globalFilter = ref('');

const table = useVueTable({
    data: props.table_rows,
    columns: props.columns,
    state: {
        get globalFilter() {
            return globalFilter.value;
        },
    },
    onGlobalFilterChange: (value) => {
        globalFilter.value = String(value);
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
});

const editSelected = (slug: string) => {
    router.get(route('admin.' + props.page_name + '.edit', slug));
};

const deleteSelected = (id: number) => {
    if (!confirm('Are you sure you want to delete it?')) return;

    router.delete(route('admin.' + props.page_name + '.destroy', id));
};
</script>

<template>
    <div class="flex flex-col gap-4 p-10">
        <div v-if="showAlert" class="grid w-full max-w-xl items-start gap-4">
            <Alert
                class="rounded-lg border border-green-600 text-green-600 shadow-sm"
            >
                <CheckCircle />
                <AlertTitle>{{
                    t('admin.' + page_name + '.' + message)
                }}</AlertTitle>
            </Alert>
        </div>

        <div class="mb-2 flex flex-wrap justify-between gap-2">
            <div class="flex gap-2">
                <Input
                    placeholder="Filter rows..."
                    class="w-64"
                    :model-value="globalFilter"
                    @update:model-value="table.setGlobalFilter($event)"
                />
            </div>
        </div>

        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-for="header in table.getHeaderGroups()[0].headers"
                            :key="header.id"
                        >
                            {{
                                typeof header.column.columnDef.header ===
                                'function'
                                    ? header.column.columnDef.header(
                                          header.getContext(),
                                      )
                                    : header.column.columnDef.header
                            }}
                        </TableHead>
                        <TableHead class="w-10"> </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                    >
                        <TableCell
                            v-for="cell in row.getVisibleCells()"
                            :key="cell.id"
                        >
                            {{
                                typeof cell.column.columnDef.cell === 'function'
                                    ? cell.column.columnDef.cell(
                                          cell.getContext(),
                                      )
                                    : cell.getValue()
                            }}
                        </TableCell>
                        <TableCell>
                            <Button
                                size="icon"
                                variant="ghost"
                                class="group h-9 w-9 cursor-pointer"
                                @click="
                                    editSelected(
                                        row.original.slug ?? row.original.code,
                                    )
                                "
                            >
                                <Pencil
                                    class="size-5 opacity-80 group-hover:opacity-100"
                                />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="group h-9 w-9 cursor-pointer"
                                @click="deleteSelected(row.original.id)"
                            >
                                <Trash
                                    class="size-5 opacity-80 group-hover:opacity-100"
                                />
                            </Button>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="table.getRowModel().rows.length === 0">
                        <TableCell
                            :colspan="columns.length + 1"
                            class="py-6 text-center text-muted-foreground"
                        >
                            No results.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <div class="flex justify-between py-4">
            <div class="text-sm text-muted-foreground">
                Page {{ table.getState().pagination.pageIndex + 1 }} of
                {{ table.getPageCount() }}
            </div>
            <div class="flex gap-2">
                <Button
                    size="sm"
                    variant="outline"
                    :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()"
                >
                    {{ t('home.previous') }}
                </Button>
                <Button
                    size="sm"
                    variant="outline"
                    :disabled="!table.getCanNextPage()"
                    @click="table.nextPage()"
                >
                    {{ t('home.next') }}
                </Button>
            </div>
        </div>

        <div class="flex justify-start">
            <Button
                size="sm"
                variant="default"
                class="cursor-pointer"
                @click="$inertia.get(route('admin.' + page_name + '.create'))"
            >
                {{ t('admin.new') }}
            </Button>
        </div>
    </div>
</template>
