<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Color } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
interface FormData {
    name: string;
    hex_code: string;
}

const props = defineProps<{
    item?: Color;
    page_name: string;
}>();

const isEdit = !!props.item?.id;

const form = useForm<FormData>({
    name: props.item?.name ?? '',
    hex_code: props.item?.hex_code ?? '#000000',
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
                <Label>{{ t('admin.hex_code') }}</Label>
                <div class="flex items-center gap-2">
                    <Input v-model="form.hex_code" type="color" class="h-10 w-16 p-1" />
                    <Input v-model="form.hex_code" type="text" placeholder="#000000" />
                </div>
                <p v-if="form.errors.hex_code" class="text-sm text-red-500">
                    {{ form.errors.hex_code }}
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
