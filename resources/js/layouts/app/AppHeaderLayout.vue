<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import PromotionBanner from '@/components/PromotionBanner.vue';
import StoreFooter from '@/components/StoreFooter.vue';
</script>

<template>
    <AppShell class="storefront min-h-screen flex-col bg-background">
        <PromotionBanner />
        <AppHeader />
        <AppContent class="w-full flex-1">
            <slot />
        </AppContent>
        <StoreFooter />
    </AppShell>
</template>
