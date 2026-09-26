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
        title: t('home.marks'),
        href: route('admin.marks.index'),
        icon: List,
    },
    {
        title: t('home.sizes'),
        href: route('admin.sizes.index'),
        icon: List,
    },
    {
        title: t('home.categories'),
        href: route('admin.categories.index'),
        icon: List,
    },
    {
        title: t('home.colors'),
        href: route('admin.colors.index'),
        icon: List,
    },
    {
        title: t('home.countries'),
        href: route('admin.countries.index'),
        icon: List,
    },
    {
        title: t('home.promotions'),
        href: route('admin.promotions.index'),
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
