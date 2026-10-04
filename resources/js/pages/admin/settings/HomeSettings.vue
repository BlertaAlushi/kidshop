<script setup lang="ts">
import AppLayout from '@/layouts/AdminLayout.vue';
import { Alert, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { type BreadcrumbItem, type PageType } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { route } from 'ziggy-js';

const { t } = useI18n();
const page = usePage<PageType>();

const props = defineProps<{
    hero_background_image?: string | null;
    hero_title?: string | null;
    hero_description?: string | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: t('admin.home_settings.title'), href: route('admin.settings.home.edit') },
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

interface FormData {
    hero_background_image: File | null;
    hero_title: string;
    hero_description: string;
}

const form = useForm<FormData>({
    hero_background_image: null,
    hero_title: props.hero_title ?? '',
    hero_description: props.hero_description ?? '',
});

const preview = ref<string | null>(null);

const handleFile = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.hero_background_image = file;
    preview.value = file ? URL.createObjectURL(file) : null;
};

const submit = () => {
    form.post(route('admin.settings.home.update'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            preview.value = null;
        },
    });
};

const removeImage = () => {
    if (!confirm('Are you sure you want to remove the background image?')) return;

    router.delete(route('admin.settings.home.destroy'));
};
</script>

<template>
    <Head :title="t('admin.home_settings.title')" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-10">
            <div v-if="showAlert" class="grid w-full max-w-xl items-start gap-4">
                <Alert class="rounded-lg border border-green-600 text-green-600 shadow-sm">
                    <CheckCircle />
                    <AlertTitle>{{ t('admin.home_settings.' + message) }}</AlertTitle>
                </Alert>
            </div>

            <Card class="mx-auto w-full max-w-3xl">
                <CardHeader>
                    <CardTitle>{{ t('admin.home_settings.title') }}</CardTitle>
                </CardHeader>

                <CardContent class="space-y-6">
                    <div class="grid w-full items-center gap-1.5">
                        <Label>{{ t('admin.home_settings.hero_title') }}</Label>
                        <Input v-model="form.hero_title" type="text" maxlength="255" />
                        <p v-if="form.errors.hero_title" class="text-sm text-red-500">
                            {{ form.errors.hero_title }}
                        </p>
                    </div>

                    <div class="grid w-full items-center gap-1.5">
                        <Label>{{ t('admin.home_settings.hero_description') }}</Label>
                        <Textarea v-model="form.hero_description" maxlength="1000" rows="3" />
                        <p v-if="form.errors.hero_description" class="text-sm text-red-500">
                            {{ form.errors.hero_description }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label>{{ t('admin.home_settings.current_image') }}</Label>
                        <div
                            v-if="preview || props.hero_background_image"
                            class="relative w-full overflow-hidden rounded-lg border"
                        >
                            <img
                                :src="preview ?? props.hero_background_image ?? ''"
                                class="max-h-80 w-full object-cover"
                                alt="Home hero background"
                            />
                        </div>
                        <p v-else class="text-sm text-muted-foreground">
                            {{ t('admin.home_settings.no_image') }}
                        </p>
                    </div>

                    <div class="grid w-full items-center gap-1.5">
                        <Label>{{ t('admin.home_settings.upload_image') }}</Label>
                        <Input type="file" accept="image/*" @change="handleFile" />
                        <p v-if="form.errors.hero_background_image" class="text-sm text-red-500">
                            {{ form.errors.hero_background_image }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Button
                            v-if="props.hero_background_image"
                            type="button"
                            variant="outline"
                            @click="removeImage"
                        >
                            <Trash2 class="size-4" />
                            {{ t('admin.home_settings.remove_image') }}
                        </Button>
                        <Button @click="submit" :disabled="form.processing">
                            {{ t('admin.update') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
