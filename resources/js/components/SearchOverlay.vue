<script setup lang="ts">
import { reactive } from 'vue';
import { Search, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Filters } from '@/types';

const props = defineProps<{
    filters: Filters;
}>();

const form = reactive({
    search: props.filters.search ?? '',
});

const emit = defineEmits<{
    (e: 'update:filters', value: Partial<Filters>): void;
}>();

function close() {
    form.search = '';
    emit('update:filters', {
        search: form.search,
    });
}

function doSearch() {
    emit('update:filters', {
        search: form.search,
    });
}
</script>

<template>
    <transition name="slide-down">
        <div v-if="form" class="w-full">
            <div class="mx-auto flex w-full max-w-5xl items-center gap-2 p-4">
                <Input
                    v-model="form.search"
                    type="text"
                    placeholder="Search products..."
                />
                <Button
                    variant="ghost"
                    size="icon"
                    class="group h-9 w-9 cursor-pointer"
                    @click="doSearch"
                >
                    <Search class="size-5 opacity-80 group-hover:opacity-100" />
                </Button>
                <Button
                    variant="ghost"
                    size="icon"
                    class="group h-9 w-9 cursor-pointer"
                    @click="close"
                >
                    <X class="size-5 opacity-80 group-hover:opacity-100" />
                </Button>
            </div>
        </div>
    </transition>
</template>
