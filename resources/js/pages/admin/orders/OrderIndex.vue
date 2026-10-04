<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import OrderStatusBadge from '@/components/OrderStatusBadge.vue';
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronRight, Search, X } from 'lucide-vue-next';
import { computed, reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();

interface OrderRow {
    id: number;
    order_number: string;
    customer_name: string;
    customer_phone: string;
    customer_email: string | null;
    status: string;
    payment_status: string;
    total_amount: string;
    items_count: number;
    created_at: string;
}

interface Paginated<T> {
    data: T[];
    from: number | null;
    to: number | null;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Filters {
    search?: string;
    status?: string;
    payment_status?: string;
    date_from?: string;
    date_to?: string;
}

const props = defineProps<{
    orders: Paginated<OrderRow>;
    filters: Filters;
    statuses: string[];
    payment_statuses: string[];
    status_counts: Record<string, number>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.orders.title'), href: route('admin.orders.index') },
];

// "all" is used as the empty value since the Select component can't hold an empty string
const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? 'all',
    payment_status: props.filters.payment_status ?? 'all',
    date_from: props.filters.date_from ?? '',
    date_to: props.filters.date_to ?? '',
});

const totalOrders = computed(() =>
    Object.values(props.status_counts).reduce((sum, n) => sum + Number(n), 0),
);

const hasFilters = computed(
    () =>
        !!form.search ||
        form.status !== 'all' ||
        form.payment_status !== 'all' ||
        !!form.date_from ||
        !!form.date_to,
);

const applyFilters = () => {
    const query: Record<string, string> = {};
    if (form.search) query.search = form.search;
    if (form.status !== 'all') query.status = form.status;
    if (form.payment_status !== 'all') query.payment_status = form.payment_status;
    if (form.date_from) query.date_from = form.date_from;
    if (form.date_to) query.date_to = form.date_to;

    router.get(route('admin.orders.index'), query, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

let searchTimeout: ReturnType<typeof setTimeout> | undefined;
watch(
    () => form.search,
    () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 350);
    },
);

watch(() => [form.status, form.payment_status, form.date_from, form.date_to], applyFilters);

const setStatus = (status: string) => {
    form.status = status;
};

const clearFilters = () => {
    clearTimeout(searchTimeout);
    Object.assign(form, {
        search: '',
        status: 'all',
        payment_status: 'all',
        date_from: '',
        date_to: '',
    });
};

const openOrder = (id: number) => {
    router.get(route('admin.orders.show', id));
};

const formatDate = (value: string) =>
    new Date(value).toLocaleString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const paginationLabel = (label: string) =>
    label.replace('&laquo; Previous', '«').replace('Next &raquo;', '»');
</script>

<template>
    <Head :title="t('admin.orders.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4 md:p-10">
            <!-- Status tabs -->
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    class="cursor-pointer rounded-full border px-3 py-1.5 text-sm transition-colors"
                    :class="
                        form.status === 'all'
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'hover:bg-accent'
                    "
                    @click="setStatus('all')"
                >
                    {{ t('admin.orders.all') }}
                    <span class="ml-1 opacity-70">{{ totalOrders }}</span>
                </button>
                <button
                    v-for="status in statuses"
                    :key="status"
                    type="button"
                    class="cursor-pointer rounded-full border px-3 py-1.5 text-sm transition-colors"
                    :class="
                        form.status === status
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'hover:bg-accent'
                    "
                    @click="setStatus(status)"
                >
                    {{ t('admin.orders.statuses.' + status) }}
                    <span class="ml-1 opacity-70">{{ status_counts[status] ?? 0 }}</span>
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-end gap-3">
                <div class="relative w-full sm:w-80">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="form.search"
                        :placeholder="t('admin.orders.search_placeholder')"
                        class="pl-8"
                    />
                </div>

                <Select v-model="form.payment_status">
                    <SelectTrigger class="w-44">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">{{ t('admin.orders.all_payments') }}</SelectItem>
                        <SelectItem v-for="ps in payment_statuses" :key="ps" :value="ps">
                            {{ t('admin.orders.payment_statuses.' + ps) }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <div class="flex items-center gap-2">
                    <label class="text-sm text-muted-foreground">{{ t('admin.orders.date_from') }}</label>
                    <Input v-model="form.date_from" type="date" class="w-40" />
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-muted-foreground">{{ t('admin.orders.date_to') }}</label>
                    <Input v-model="form.date_to" type="date" class="w-40" :min="form.date_from || undefined" />
                </div>

                <Button v-if="hasFilters" variant="ghost" size="sm" class="cursor-pointer" @click="clearFilters">
                    <X class="size-4" />
                    {{ t('admin.orders.clear_filters') }}
                </Button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>{{ t('admin.orders.order_number') }}</TableHead>
                            <TableHead>{{ t('admin.orders.customer') }}</TableHead>
                            <TableHead>{{ t('admin.orders.date') }}</TableHead>
                            <TableHead class="text-center">{{ t('admin.orders.items') }}</TableHead>
                            <TableHead class="text-right">{{ t('admin.orders.total') }}</TableHead>
                            <TableHead>{{ t('admin.orders.status') }}</TableHead>
                            <TableHead>{{ t('admin.orders.payment') }}</TableHead>
                            <TableHead class="w-10"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="order in orders.data"
                            :key="order.id"
                            class="cursor-pointer"
                            @click="openOrder(order.id)"
                        >
                            <TableCell class="font-medium">{{ order.order_number }}</TableCell>
                            <TableCell>
                                <div>{{ order.customer_name }}</div>
                                <div class="text-xs text-muted-foreground">{{ order.customer_phone }}</div>
                            </TableCell>
                            <TableCell class="whitespace-nowrap">{{ formatDate(order.created_at) }}</TableCell>
                            <TableCell class="text-center">{{ order.items_count }}</TableCell>
                            <TableCell class="text-right whitespace-nowrap font-medium">
                                {{ order.total_amount }} €
                            </TableCell>
                            <TableCell><OrderStatusBadge :status="order.status" /></TableCell>
                            <TableCell><OrderStatusBadge :status="order.payment_status" type="payment" /></TableCell>
                            <TableCell>
                                <ChevronRight class="size-4 text-muted-foreground" />
                            </TableCell>
                        </TableRow>

                        <TableRow v-if="orders.data.length === 0">
                            <TableCell colspan="8" class="py-10 text-center text-muted-foreground">
                                {{ t('admin.orders.no_results') }}
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div v-if="orders.total > 0" class="flex flex-wrap items-center justify-between gap-3">
                <div class="text-sm text-muted-foreground">
                    {{ t('admin.orders.showing', { from: orders.from, to: orders.to, total: orders.total }) }}
                </div>
                <div v-if="orders.links.length > 3" class="flex flex-wrap gap-1">
                    <template v-for="(link, i) in orders.links" :key="i">
                        <Button
                            v-if="link.url"
                            as-child
                            size="sm"
                            :variant="link.active ? 'default' : 'outline'"
                        >
                            <Link :href="link.url" preserve-scroll preserve-state>
                                {{ paginationLabel(link.label) }}
                            </Link>
                        </Button>
                        <Button v-else size="sm" variant="outline" disabled>
                            {{ paginationLabel(link.label) }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
