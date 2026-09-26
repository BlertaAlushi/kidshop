<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Country } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
interface FormData {
    iso_2: string;
    country: string;
    delivery_fee: number;
}

const props = defineProps<{
    item?: Country;
    page_name: string;
}>();

const isEdit = !!props.item?.iso_2;

const form = useForm<FormData>({
    iso_2: props.item?.iso_2 ?? '',
    country: props.item?.country ?? '',
    delivery_fee: props.item?.delivery_fee ?? 0,
});

const submit = () => {
    if (isEdit) {
        form.put(route('admin.' + props.page_name + '.update', props.item.iso_2));
    } else {
        form.post(route('admin.' + props.page_name + '.store'));
    }
};
</script>

<template>
    <Card class="mx-auto mt-10 w-full max-w-5xl">
        <CardHeader>
            <CardTitle>
                {{
                    isEdit
                        ? t('admin.' + page_name + '.edit')
                        : t('admin.' + page_name + '.new')
                }}
            </CardTitle>
        </CardHeader>

        <CardContent class="space-y-6">
            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label>{{ t('admin.iso_2') }}</Label>
                <Input
                    v-model="form.iso_2"
                    type="text"
                    maxlength="2"
                    :disabled="isEdit"
                    @input="form.iso_2 = form.iso_2.toUpperCase()"
                />
                <p v-if="form.errors.iso_2" class="text-sm text-red-500">
                    {{ form.errors.iso_2 }}
                </p>
            </div>

            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label>{{ t('admin.name') }}</Label>
                <Input v-model="form.country" type="text" />
                <p v-if="form.errors.country" class="text-sm text-red-500">
                    {{ form.errors.country }}
                </p>
            </div>

            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label>{{ t('admin.delivery_fee') }}</Label>
                <Input v-model="form.delivery_fee" type="number" step="0.01" min="0" />
                <p v-if="form.errors.delivery_fee" class="text-sm text-red-500">
                    {{ form.errors.delivery_fee }}
                </p>
            </div>

            <div class="flex justify-end">
                <Button
                    @click="submit"
                    :disabled="form.processing"
                    class="cursor:pointer"
                >
                    {{ isEdit ? t('admin.update') : t('admin.create') }}
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
