<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Size } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
interface FormData {
    name: string;
    sort_order: number;
}

const props = defineProps<{
    item?: Size;
    page_name: string;
}>();

const isEdit = !!props.item?.id;

const form = useForm<FormData>({
    name: props.item?.name ?? '',
    sort_order: props.item?.sort_order ?? 0,
});

const submit = () => {
    if (isEdit) {
        form.put(route('admin.' + props.page_name + '.update', props.item.id));
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
                <Label>{{ t('admin.default_name') }}</Label>
                <Input v-model="form.name" type="text" />
                <p v-if="form.errors.name" class="text-sm text-red-500">
                    {{ form.errors.name }}
                </p>
            </div>

            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label>{{ t('admin.sort_order') }}</Label>
                <Input v-model.number="form.sort_order" type="number" min="0" />
                <p v-if="form.errors.sort_order" class="text-sm text-red-500">
                    {{ form.errors.sort_order }}
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
