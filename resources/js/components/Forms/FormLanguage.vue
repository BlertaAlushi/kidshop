<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Language } from '@/types';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
interface FormData {
    code?: string;
    language: string;
}

const props = defineProps<{
    item?: Language;
    page_name: string;
}>();

const isEdit = !!props.item?.id;

const form = useForm<FormData>({
    code: props.item?.code ?? '',
    language: props.item?.language ?? '',
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
                <Label>{{ t('admin.language') }}</Label>
                <Input v-model="form.language" type="text" />
                <p v-if="form.errors.language" class="text-sm text-red-500">
                    {{ form.errors.language }}
                </p>
            </div>

            <div class="grid w-full max-w-sm items-center gap-1.5">
                <Label>{{ t('admin.code') }}</Label>
                <Input v-model="form.code" type="text" />
                <p v-if="form.errors.code" class="text-sm text-red-500">
                    {{ form.errors.code }}
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
