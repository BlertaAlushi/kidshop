<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import OrderStatusBadge from '@/components/OrderStatusBadge.vue';
import { Alert, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
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
import { type BreadcrumbItem, type PageType } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle, ImageOff } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();
const page = usePage<PageType>();

interface OrderItem {
    id: number;
    product_name: string;
    size_name: string | null;
    color_name: string | null;
    original_unit_price: string;
    discount_amount: string;
    promotion_name: string | null;
    unit_price: string;
    quantity: number;
    total: string;
    image_url: string | null;
}

interface OrderAddress {
    id: number;
    type: string;
    first_name: string;
    last_name: string;
    phone: string;
    address: string;
    city: string;
    postal_code: string | null;
    country: string;
    country_details: { country: string } | null;
}

interface Order {
    id: number;
    order_number: string;
    customer_name: string;
    customer_phone: string;
    customer_email: string | null;
    notes: string | null;
    status: string;
    payment_method: string;
    payment_status: string;
    total_amount: string;
    delivery_fee: string;
    created_at: string;
    items: OrderItem[];
    addresses: OrderAddress[];
}

const props = defineProps<{
    order: Order;
    statuses: string[];
    payment_statuses: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.orders.title'), href: route('admin.orders.index') },
    { title: props.order.order_number, href: route('admin.orders.show', props.order.id) },
];

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

const form = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
});

const submit = () => {
    form.put(route('admin.orders.update', props.order.id), {
        preserveScroll: true,
        onSuccess: () => form.defaults(),
    });
};

const shippingAddress = computed(
    () => props.order.addresses.find((a) => a.type === 'shipping') ?? props.order.addresses[0] ?? null,
);

const subtotal = computed(() =>
    props.order.items.reduce((sum, item) => sum + Number(item.total), 0).toFixed(2),
);

const zoomedItem = ref<OrderItem | null>(null);

const formatDate = (value: string) =>
    new Date(value).toLocaleString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
</script>

<template>
    <Head :title="`${t('admin.orders.order')} ${order.order_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-10">
            <div v-if="showAlert" class="grid w-full max-w-xl items-start gap-4">
                <Alert class="rounded-lg border border-green-600 text-green-600 shadow-sm">
                    <CheckCircle />
                    <AlertTitle>{{ t('admin.orders.' + message) }}</AlertTitle>
                </Alert>
            </div>

            <!-- Header -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <Link
                        :href="route('admin.orders.index')"
                        class="mb-2 inline-flex items-center gap-1 text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft class="size-4" />
                        {{ t('admin.orders.back') }}
                    </Link>
                    <h1 class="text-2xl font-semibold">{{ order.order_number }}</h1>
                    <p class="text-sm text-muted-foreground">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="flex gap-2">
                    <OrderStatusBadge :status="order.status" />
                    <OrderStatusBadge :status="order.payment_status" type="payment" />
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Items -->
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>{{ t('admin.orders.items') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>{{ t('admin.orders.product') }}</TableHead>
                                        <TableHead class="text-right">{{ t('admin.orders.unit_price') }}</TableHead>
                                        <TableHead class="text-center">{{ t('admin.orders.quantity') }}</TableHead>
                                        <TableHead class="text-right">{{ t('admin.orders.total') }}</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in order.items" :key="item.id">
                                        <TableCell>
                                            <div class="flex items-center gap-3">
                                                <button
                                                    v-if="item.image_url"
                                                    type="button"
                                                    class="shrink-0 cursor-zoom-in rounded-md transition-opacity hover:opacity-80"
                                                    @click="zoomedItem = item"
                                                >
                                                    <img
                                                        :src="item.image_url"
                                                        :alt="item.product_name"
                                                        class="size-14 rounded-md border object-cover"
                                                    />
                                                </button>
                                                <div
                                                    v-else
                                                    class="flex size-14 shrink-0 items-center justify-center rounded-md border bg-muted text-muted-foreground"
                                                >
                                                    <ImageOff class="size-5" />
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ item.product_name }}</div>
                                                    <div class="text-xs text-muted-foreground">
                                                        <span v-if="item.size_name">{{ t('admin.orders.size') }}: {{ item.size_name }}</span>
                                                        <span v-if="item.size_name && item.color_name"> · </span>
                                                        <span v-if="item.color_name">{{ t('admin.orders.color') }}: {{ item.color_name }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-right whitespace-nowrap">
                                            <div>{{ item.unit_price }} €</div>
                                            <div
                                                v-if="Number(item.discount_amount) > 0"
                                                class="text-xs text-muted-foreground line-through"
                                            >
                                                {{ item.original_unit_price }} €
                                            </div>
                                            <div v-if="item.promotion_name" class="text-xs text-muted-foreground">
                                                {{ item.promotion_name }}
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-center">{{ item.quantity }}</TableCell>
                                        <TableCell class="text-right whitespace-nowrap font-medium">{{ item.total }} €</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <div class="mt-4 ml-auto max-w-xs space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">{{ t('admin.orders.subtotal') }}</span>
                                <span>{{ subtotal }} €</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">{{ t('admin.orders.delivery_fee') }}</span>
                                <span>{{ order.delivery_fee }} €</span>
                            </div>
                            <div class="flex justify-between border-t pt-2 text-base font-semibold">
                                <span>{{ t('admin.orders.total') }}</span>
                                <span>{{ order.total_amount }} €</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex flex-col gap-6">
                    <!-- Status update -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('admin.orders.update_status') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-1.5">
                                <Label>{{ t('admin.orders.status') }}</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger class="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="status in statuses" :key="status" :value="status">
                                            {{ t('admin.orders.statuses.' + status) }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="grid gap-1.5">
                                <Label>{{ t('admin.orders.payment_status') }}</Label>
                                <Select v-model="form.payment_status">
                                    <SelectTrigger class="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="ps in payment_statuses" :key="ps" :value="ps">
                                            {{ t('admin.orders.payment_statuses.' + ps) }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Button
                                class="w-full cursor-pointer"
                                :disabled="form.processing || !form.isDirty"
                                @click="submit"
                            >
                                {{ t('admin.orders.save') }}
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Customer -->
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t('admin.orders.customer_info') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-1 text-sm">
                            <div class="font-medium">{{ order.customer_name }}</div>
                            <div>
                                <a :href="`tel:${order.customer_phone}`" class="hover:underline">{{ order.customer_phone }}</a>
                            </div>
                            <div v-if="order.customer_email">
                                <a :href="`mailto:${order.customer_email}`" class="hover:underline">{{ order.customer_email }}</a>
                            </div>
                            <div class="pt-2 text-muted-foreground">
                                {{ t('admin.orders.payment_method') }}:
                                {{ t('admin.orders.payment_methods.' + order.payment_method) }}
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Shipping address -->
                    <Card v-if="shippingAddress">
                        <CardHeader>
                            <CardTitle>{{ t('admin.orders.shipping_address') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-1 text-sm">
                            <div>{{ shippingAddress.address }}</div>
                            <div>
                                {{ shippingAddress.city }}<span v-if="shippingAddress.postal_code">, {{ shippingAddress.postal_code }}</span>
                            </div>
                            <div>{{ shippingAddress.country_details?.country ?? shippingAddress.country }}</div>
                        </CardContent>
                    </Card>

                    <!-- Notes -->
                    <Card v-if="order.notes">
                        <CardHeader>
                            <CardTitle>{{ t('admin.orders.notes') }}</CardTitle>
                        </CardHeader>
                        <CardContent class="text-sm whitespace-pre-line">{{ order.notes }}</CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <Dialog :open="!!zoomedItem" @update:open="(open) => !open && (zoomedItem = null)">
            <DialogContent class="p-2 sm:max-w-3xl">
                <DialogTitle class="sr-only">{{ zoomedItem?.product_name }}</DialogTitle>
                <img
                    v-if="zoomedItem?.image_url"
                    :src="zoomedItem.image_url"
                    :alt="zoomedItem.product_name"
                    class="max-h-[85vh] w-full rounded-md object-contain"
                />
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
