<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import UserHeader from '@/components/UserHeader.vue';
import AppShell from '@/components/AppShell.vue';
</script>

<template>
    <AppShell class="flex-col">
        <UserHeader />
        <AppContent>
            <slot />
        </AppContent>
    </AppShell>
</template>
