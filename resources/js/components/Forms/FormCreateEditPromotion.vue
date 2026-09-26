<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Trash } from 'lucide-vue-next';
import { Color, Promotion, PromotionTargetForm } from '@/types';

const { t } = useI18n();

interface OptionItem {
    id: number;
    name: string;
}

interface PromotionFormData {
    name: string;
    type: 'percentage' | 'fixed';
    value: number;
    starts_at: string;
    ends_at: string;
    is_active: boolean;
    targets: PromotionTargetForm[];
}

const props = defineProps<{
    promotion?: Promotion;
    products: OptionItem[];
    categories: OptionItem[];
    brands: OptionItem[];
    colors: Color[];
}>();

const isEdit = !!props.promotion?.id;

const toDatetimeLocal = (value?: string | null) => (value ? value.slice(0, 16) : '');

const emptyTarget = (): PromotionTargetForm => ({
    type: 'product',
    target_id: null,
    color_id: null,
});

const form = useForm<PromotionFormData>({
    name: props.promotion?.name ?? '',
    type: props.promotion?.type ?? 'percentage',
    value: props.promotion?.value ?? 0,
    starts_at: toDatetimeLocal(props.promotion?.starts_at),
    ends_at: toDatetimeLocal(props.promotion?.ends_at),
    is_active: props.promotion?.is_active ?? true,

    targets: props.promotion?.targets?.length
        ? props.promotion.targets.map((target) => ({
              id: target.id,
              type: target.type,
              target_id: target.target_id,
              color_id: target.color_id,
          }))
        : [emptyTarget()],
});

const targetOptions = (type: PromotionTargetForm['type']) => {
    if (type === 'product') return props.products;
    if (type === 'category') return props.categories;
    return props.brands;
};

const onTargetTypeChange = (target: PromotionTargetForm, value: PromotionTargetForm['type']) => {
    target.type = value;
    target.target_id = null;

    if (value !== 'product') {
        target.color_id = null;
    }
};

const addTarget = () => form.targets.push(emptyTarget());
const removeTarget = (index: number) => form.targets.splice(index, 1);

const submit = () => {
    if (isEdit) {
        form.put(route('admin.promotions.update', props.promotion!.id));
    } else {
        form.post(route('admin.promotions.store'));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="mx-auto w-full max-w-4xl space-y-6 p-10">
        <Card>
            <CardHeader>
                <CardTitle>
                    {{ isEdit ? t('admin.promotions.edit') : t('admin.promotions.new') }}
                </CardTitle>
            </CardHeader>
            <CardContent class="grid grid-cols-2 gap-4">
                <div class="col-span-2 grid w-full max-w-sm items-center gap-1.5">
                    <Label>{{ t('admin.name') }}</Label>
                    <Input v-model="form.name" type="text" />
                    <p v-if="form.errors.name" class="text-sm text-red-500">
                        {{ form.errors.name }}
                    </p>
                </div>

                <div class="grid w-full max-w-sm items-center gap-1.5">
                    <Label>{{ t('admin.type') }}</Label>
                    <Select v-model="form.type">
                        <SelectTrigger>
                            <SelectValue :placeholder="t('admin.type')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="percentage">{{ t('admin.percentage') }}</SelectItem>
                            <SelectItem value="fixed">{{ t('admin.fixed') }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.type" class="text-sm text-red-500">
                        {{ form.errors.type }}
                    </p>
                </div>

                <div class="grid w-full max-w-sm items-center gap-1.5">
                    <Label>{{ t('admin.value') }}</Label>
                    <Input v-model.number="form.value" type="number" min="0" step="0.01" />
                    <p v-if="form.errors.value" class="text-sm text-red-500">
                        {{ form.errors.value }}
                    </p>
                </div>

                <div class="grid w-full max-w-sm items-center gap-1.5">
                    <Label>{{ t('admin.starts_at') }}</Label>
                    <Input v-model="form.starts_at" type="datetime-local" />
                    <p v-if="form.errors.starts_at" class="text-sm text-red-500">
                        {{ form.errors.starts_at }}
                    </p>
                </div>

                <div class="grid w-full max-w-sm items-center gap-1.5">
                    <Label>{{ t('admin.ends_at') }}</Label>
                    <Input v-model="form.ends_at" type="datetime-local" />
                    <p v-if="form.errors.ends_at" class="text-sm text-red-500">
                        {{ form.errors.ends_at }}
                    </p>
                </div>

                <div class="grid w-full max-w-sm items-center gap-1.5">
                    <Label for="is_active" class="flex items-center gap-3">
                        <Switch
                            id="is_active"
                            :model-value="form.is_active"
                            @update:model-value="form.is_active = $event"
                        />
                        <span>{{ t('admin.is_active') }}</span>
                    </Label>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>{{ t('admin.targets') }}</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div
                    v-for="(target, index) in form.targets"
                    :key="index"
                    class="grid grid-cols-4 items-end gap-3 border-b pb-4 last:border-b-0"
                >
                    <div class="grid gap-1.5">
                        <Label>{{ t('admin.target_type') }}</Label>
                        <Select
                            :model-value="target.type"
                            @update:model-value="onTargetTypeChange(target, $event as PromotionTargetForm['type'])"
                        >
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.select')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="product">{{ t('admin.product') }}</SelectItem>
                                <SelectItem value="category">{{ t('admin.category') }}</SelectItem>
                                <SelectItem value="brand">{{ t('home.brand') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors[`targets.${index}.type`]" class="text-sm text-red-500">
                            {{ form.errors[`targets.${index}.type`] }}
                        </p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label>{{ t('admin.target') }}</Label>
                        <Select v-model="target.target_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.select')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="option in targetOptions(target.type)"
                                    :key="option.id"
                                    :value="option.id"
                                >
                                    {{ option.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors[`targets.${index}.target_id`]" class="text-sm text-red-500">
                            {{ form.errors[`targets.${index}.target_id`] }}
                        </p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label>{{ t('home.colors') }}</Label>
                        <Select v-if="target.type === 'product'" v-model="target.color_id">
                            <SelectTrigger>
                                <SelectValue :placeholder="t('admin.none')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="color in colors" :key="color.id" :value="color.id">
                                    {{ color.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-else class="text-sm text-muted-foreground">{{ t('admin.none') }}</p>
                        <p v-if="form.errors[`targets.${index}.color_id`]" class="text-sm text-red-500">
                            {{ form.errors[`targets.${index}.color_id`] }}
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            class="cursor-pointer"
                            :disabled="form.targets.length === 1"
                            @click="removeTarget(index)"
                        >
                            <Trash class="size-4" />
                        </Button>
                    </div>
                </div>

                <p v-if="form.errors.targets" class="text-sm text-red-500">
                    {{ form.errors.targets }}
                </p>

                <Button type="button" variant="outline" class="cursor-pointer" @click="addTarget">
                    {{ t('admin.add') }} {{ t('admin.target') }}
                </Button>
            </CardContent>
        </Card>

        <div class="flex justify-end">
            <Button @click="submit" :disabled="form.processing" class="cursor-pointer">
                {{ isEdit ? t('admin.update') : t('admin.create') }}
            </Button>
        </div>
    </form>
</template>
