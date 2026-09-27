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
            <div
                class="flex w-full items-center gap-2 rounded-full border border-border bg-card p-1.5 pl-4 shadow-sm"
            >
                <Search class="size-4 shrink-0 text-muted-foreground" />
                <Input
                    v-model="form.search"
                    type="text"
                    placeholder="Search products..."
                    class="h-8 border-none bg-transparent shadow-none focus-visible:ring-0"
                    @keyup.enter="doSearch"
                />
                <Button
                    size="sm"
                    class="h-8 shrink-0 cursor-pointer rounded-full px-4"
                    @click="doSearch"
                >
                    Search
                </Button>
                <Button
                    variant="ghost"
                    size="icon"
                    class="group h-8 w-8 shrink-0 cursor-pointer rounded-full"
                    @click="close"
                >
                    <X class="size-4 opacity-80 group-hover:opacity-100" />
                </Button>
            </div>
        </div>
    </transition>
</template>
