<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, List } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutGrid,
    },
    {
        title: t('home.products'),
        href: route('admin.products.index'),
        icon: List,
    },
    {
        title: t('home.body_parts'),
        href: route('admin.body-parts.index'),
        icon: List,
    },
    {
        title: t('home.skin_types'),
        href: route('admin.skin-types.index'),
        icon: List,
    },
    {
        title: t('home.skin_concerns'),
        href: route('admin.skin-concerns.index'),
        icon: List,
    },
    {
        title: t('home.product_types'),
        href: route('admin.product-types.index'),
        icon: List,
    },
    {
        title: t('home.extra'),
        href: route('admin.extras.index'),
        icon: List,
    },
    {
        title: t('home.marks'),
        href: route('admin.marks.index'),
        icon: List,
    },
    {
        title: t('home.languages'),
        href: route('admin.languages.index'),
        icon: List,
    },
]);

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('home')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
