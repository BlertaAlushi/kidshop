<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectTrigger,
    SelectValue,
    SelectContent,
    SelectItem,
} from '@/components/ui/select';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { PageType, ProductForm } from '@/types';
import { Label } from '@/components/ui/label';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const page = usePage<PageType>();

const languages = computed(() => page.props.languages ?? []);
const filters = computed(() => page.props.menu ?? []);

const props = defineProps<{
    product?: ProductForm;
}>();

const isEdit = !!props.product?.id;

const imagePreview = ref(props.product?.image || null);

const form = useForm<ProductForm>({
    name: props.product?.name || '',
    slug: props.product?.slug || '',
    description: props.product?.description || '',

    price: props.product?.price || 0,
    currency: props.product?.currency || 'EUR',
    stock_quantity: props.product?.stock_quantity || 0,
    mark_id: props.product?.mark_id || null,
    image: null,

    translations: languages.value.map((lang) => {
        const existing = props.product?.translations?.find(
            (t: any) => t.language_id === lang.id,
        );

        return {
            language_id: lang.id,
            name: existing?.name || '',
            description: existing?.description || '',
        };
    }),

    body_parts: props.product?.body_parts?.map((i: any) => i.id) || [],
    product_types: props.product?.product_types?.map((i: any) => i.id) || [],
    skin_types: props.product?.skin_types?.map((i: any) => i.id) || [],
    skin_concerns: props.product?.skin_concerns?.map((i: any) => i.id) || [],
    extras: props.product?.extras?.map((i: any) => i.id) || [],
});

const handleImage = (e: any) => {
    const file = e.target.files[0];
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(
        isEdit
            ? route('admin.products.update', props.product.id)
            : route('admin.products.store'),
        { forceFormData: true },
    );
};
</script>

<template>
    <form
        @submit.prevent="submit"
        class="mx-auto grid w-full grid-cols-3 gap-6 p-10"
    >
        <!-- LEFT SIDE -->
        <div class="col-span-2 space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle> {{ t('admin.product') }} </CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-2 gap-4">
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.default_name') }}</Label>
                        <Input v-model="form.name" type="text" />
                        <p v-if="form.errors.name" class="text-sm text-red-500">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.default_description') }}</Label>
                        <Textarea
                            v-model="form.description"
                            class="min-h-30"
                            placeholder="Description"
                        />
                        <p
                            v-if="form.errors.description"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <div
                        v-if="product?.id"
                        class="grid w-full max-w-sm items-center gap-1.5"
                    >
                        <Label>{{ t('admin.slug') }}</Label>
                        <Input v-model="form.slug" type="text" disabled />
                        <p v-if="form.errors.slug" class="text-sm text-red-500">
                            {{ form.errors.slug }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.stock_quantity') }}</Label>
                        <Input v-model="form.stock_quantity" type="number" />
                        <p
                            v-if="form.errors.stock_quantity"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.stock_quantity }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.price') }}</Label>
                        <Input v-model="form.price" type="number" />
                        <p
                            v-if="form.errors.price"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.price }}
                        </p>
                    </div>

                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('admin.currency') }}</Label>
                        <Input v-model="form.currency" type="text" />
                        <p
                            v-if="form.errors.currency"
                            class="text-sm text-red-500"
                        >
                            {{ form.errors.currency }}
                        </p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.translations') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Tabs default-value="0">
                        <TabsList>
                            <TabsTrigger
                                v-for="(lang, index) in languages"
                                :key="lang.id"
                                :value="index.toString()"
                            >
                                {{ lang.code.toUpperCase() }}
                            </TabsTrigger>
                        </TabsList>

                        <TabsContent
                            v-for="(lang, index) in languages"
                            :key="lang.id"
                            :value="index.toString()"
                            class="mt-4 space-y-4"
                        >
                            <Input
                                v-model="form.translations[index].name"
                                :placeholder="t('admin.name')"
                            />
                            <p
                                v-if="form.errors[`translations.${index}.name`]"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors[`translations.${index}.name`] }}
                            </p>

                            <Textarea
                                v-model="form.translations[index].description"
                                class="min-h-30"
                                :placeholder="t('admin.description')"
                            />
                            <p
                                v-if="
                                    form.errors[
                                        `translations.${index}.description`
                                    ]
                                "
                                class="text-sm text-red-500"
                            >
                                {{
                                    form.errors[
                                        `translations.${index}.description`
                                    ]
                                }}
                            </p>
                        </TabsContent>
                    </Tabs>
                </CardContent>
            </Card>

            <!-- Relations -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('admin.relations') }}</CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-2 gap-4">
                    <!-- Body Parts -->
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.body_parts') }}</Label>
                        <Select v-model="form.body_parts" multiple>
                            <SelectTrigger>
                                <SelectValue
                                    :placeholder="
                                        t('admin.select') +
                                        ' ' +
                                        t('home.body_parts')
                                    "
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="item in filters.bodyParts.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Product Types -->
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.product_types') }}</Label>
                        <Select v-model="form.product_types" multiple>
                            <SelectTrigger>
                                <SelectValue
                                    :placeholder="
                                        t('admin.select') +
                                        ' ' +
                                        t('home.product_types')
                                    "
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="item in filters.productTypes.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Skin Types -->
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.skin_types') }}</Label>
                        <Select v-model="form.skin_types" multiple>
                            <SelectTrigger>
                                <SelectValue
                                    :placeholder="
                                        t('admin.select') +
                                        ' ' +
                                        t('home.skin_types')
                                    "
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="item in filters.skinTypes.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Skin Concerns -->
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.skin_concerns') }}</Label>
                        <Select v-model="form.skin_concerns" multiple>
                            <SelectTrigger>
                                <SelectValue
                                    :placeholder="
                                        t('admin.select') +
                                        ' ' +
                                        t('home.skin_concerns')
                                    "
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="item in filters.skinConcerns.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Extras -->
                    <div class="grid w-full max-w-sm items-center gap-1.5">
                        <Label>{{ t('home.extra') }}</Label>
                        <Select v-model="form.extras" multiple>
                            <SelectTrigger>
                                <SelectValue
                                    :placeholder="
                                        t('admin.select') +
                                        ' ' +
                                        t('home.extra')
                                    "
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="item in filters.extras.data"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle
                        >{{ t('admin.product') }}
                        {{ t('admin.image') }}</CardTitle
                    >
                </CardHeader>
                <CardContent class="space-y-4">
                    <Input type="file" @change="handleImage" />
                    <img
                        v-if="imagePreview"
                        :src="imagePreview"
                        class="w-full rounded-lg border"
                        alt="Product Image"
                    />
                    <p v-if="form.errors.image" class="text-sm text-red-500">
                        {{ form.errors.image }}
                    </p>
                </CardContent>
            </Card>

            <!-- Brand -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('home.brand') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Select v-model="form.mark_id">
                        <SelectTrigger>
                            <SelectValue
                                :placeholder="t('admin.select')+' '+ t('home.brand')"
                            />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="mark in filters.marks.data"
                                :key="mark.id"
                                :value="mark.id"
                            >
                                {{ mark.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.mark_id" class="text-sm text-red-500">
                        {{ form.errors.mark_id }}
                    </p>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button
                    @click="submit"
                    :disabled="form.processing"
                    class="cursor:pointer"
                >
                    {{ isEdit ? t('admin.update') : t('admin.create') }}
                </Button>
            </div>
        </div>
    </form>
</template>
