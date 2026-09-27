<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const year = new Date().getFullYear();
</script>

<template>
    <footer class="mt-auto border-t border-border/70">
        <div
            class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-6 py-12 sm:flex-row sm:items-start sm:justify-between"
        >
            <div class="flex items-center gap-2">
                <AppLogoIcon class="size-6 fill-current text-foreground" />
                <span class="text-sm font-semibold tracking-tight"
                    >Kids Shop</span
                >
            </div>

            <nav class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                <Link
                    :href="route('home')"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ t('home.home') }}
                </Link>
                <Link
                    :href="route('collection.all')"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ t('home.products') }}
                </Link>
                <Link
                    :href="route('cart.index')"
                    class="text-muted-foreground transition-colors hover:text-foreground"
                >
                    {{ t('home.cart') }}
                </Link>
            </nav>
        </div>

        <div class="border-t border-border/70">
            <p
                class="mx-auto w-full max-w-7xl px-6 py-5 text-xs text-muted-foreground"
            >
                &copy; {{ year }} Kids Shop.
            </p>
        </div>
    </footer>
</template>
