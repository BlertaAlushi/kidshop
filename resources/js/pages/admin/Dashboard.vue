<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Euro, ShoppingBag, Tag } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import {route} from 'ziggy-js'

const { t } = useI18n();

interface PromotionSale {
    promotion_id: number | null;
    promotion_name: string;
    order_count: number;
    units_sold: number;
    revenue: number;
    discount_given: number;
}

defineProps<{
    order_count: number;
    revenue_total: number;
    revenue_paid: number;
    statuses: string[];
    status_counts: Record<string, number>;
    promotion_sales: PromotionSale[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="flex items-center justify-between text-sm text-muted-foreground">
                        <span>{{ t('admin.orders.total_orders') }}</span>
                        <ShoppingBag class="h-4 w-4" />
                    </div>
                    <Link :href="route('admin.orders.index')" class="mt-2 block text-3xl font-semibold hover:underline">
                        {{ order_count }}
                    </Link>
                    <div class="mt-3 flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted-foreground">
                        <Link
                            v-for="status in statuses"
                            :key="status"
                            :href="route('admin.orders.index', { status })"
                            class="hover:text-foreground hover:underline"
                        >
                            {{ t('admin.orders.statuses.' + status) }}: {{ status_counts[status] ?? 0 }}
                        </Link>
                    </div>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="flex items-center justify-between text-sm text-muted-foreground">
                        <span>{{ t('admin.orders.revenue_paid') }}</span>
                        <Euro class="h-4 w-4" />
                    </div>
                    <div class="mt-2 text-3xl font-semibold">{{ revenue_paid.toFixed(2) }} €</div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        {{ t('admin.orders.total_revenue') }}: {{ revenue_total.toFixed(2) }} €
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                <div class="flex items-center justify-between text-sm text-muted-foreground">
                    <span>{{ t('admin.promotions.sales_title') }}</span>
                    <Tag class="h-4 w-4" />
                </div>
                <p v-if="!promotion_sales.length" class="mt-3 text-sm text-muted-foreground">
                    {{ t('admin.promotions.sales_empty') }}
                </p>
                <div v-else class="mt-2 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>{{ t('admin.promotions.sales_promotion') }}</TableHead>
                                <TableHead class="text-right">{{ t('admin.promotions.sales_orders') }}</TableHead>
                                <TableHead class="text-right">{{ t('admin.promotions.sales_units') }}</TableHead>
                                <TableHead class="text-right">{{ t('admin.promotions.sales_revenue') }}</TableHead>
                                <TableHead class="text-right">{{ t('admin.promotions.sales_discount') }}</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="sale in promotion_sales" :key="sale.promotion_id ?? 'deleted'">
                                <TableCell class="font-medium">
                                    <Link
                                        v-if="sale.promotion_id"
                                        :href="route('admin.promotions.edit', sale.promotion_id)"
                                        class="hover:underline"
                                    >
                                        {{ sale.promotion_name }}
                                    </Link>
                                    <span v-else class="text-muted-foreground">
                                        {{ t('admin.promotions.sales_deleted') }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-right">{{ sale.order_count }}</TableCell>
                                <TableCell class="text-right">{{ sale.units_sold }}</TableCell>
                                <TableCell class="text-right whitespace-nowrap">{{ sale.revenue.toFixed(2) }} €</TableCell>
                                <TableCell class="text-right whitespace-nowrap text-muted-foreground">
                                    −{{ sale.discount_given.toFixed(2) }} €
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
