<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <div>
        <div>
            <div class="flex h-20 items-center p-6">
                <Link :href="route('home')" class="flex items-center gap-x-2">
                    <AppLogo />
                </Link>

            </div>
        </div>
    </div>
</template>
