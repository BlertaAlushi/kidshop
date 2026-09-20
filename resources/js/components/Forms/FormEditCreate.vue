<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { usePage } from '@inertiajs/vue3';
import { Item, PageType, Translation } from '@/types';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const page = usePage<PageType>();
const { t } = useI18n();

const languages = computed(() => page.props.languages ?? []);
interface FormData {
    slug?: string;
    name: string;
    translations: Translation[];
}

const props = defineProps<{
    item?: Item;
    page_name: string;
}>();

const isEdit = !!props.item?.id;

const translationsArray: Translation[] = languages.value.map((lang) => {
    const existing = props.item?.translations?.find(
        (t: any) => t.language_id === lang.id,
    );

    return {
        language_id: lang.id,
        name: existing?.name ?? '',
    };
});

const form = useForm<FormData>({
    slug: props.item?.slug ?? '',
    name: props.item?.name ?? '',
    translations: translationsArray,
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

            <div
                v-if="item?.id"
                class="grid w-full max-w-sm items-center gap-1.5"
            >
                <Label>{{ t('admin.slug') }}</Label>
                <Input v-model="form.slug" type="text" disabled />
                <p v-if="form.errors.slug" class="text-sm text-red-500">
                    {{ form.errors.slug }}
                </p>
            </div>

            <Tabs class="w-full">
                <TabsList>
                    <TabsTrigger
                        v-for="lang in languages"
                        :key="lang.id"
                        :value="String(lang.id)"
                    >
                        {{ lang.language }}
                    </TabsTrigger>
                </TabsList>

                <TabsContent
                    v-for="(translation, index) in form.translations"
                    :key="translation.language_id"
                    :value="String(translation.language_id)"
                    class="mt-4"
                >
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>
                            {{ t('admin.name') }} ({{
                                languages.find(
                                    (l) => l.id === translation.language_id,
                                )?.language
                            }})
                        </Label>

                        <Input v-model="form.translations[index].name" />

                        <p
                            v-if="form.errors[`translations.${index}.name`]"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors[`translations.${index}.name`] }}
                        </p>
                    </div>
                </TabsContent>
            </Tabs>

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
