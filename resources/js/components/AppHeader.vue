<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { ShoppingBag } from 'lucide-vue-next';
import { MenuItem } from '@/types';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    NavigationMenuContent,
    NavigationMenuTrigger,
    NavigationMenuLink,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';

import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { toUrl, urlIsActive } from '@/lib/utils';
import type { NavItem, PageType } from '@/types';
import { InertiaLinkProps, Link } from '@inertiajs/vue3';
import { Menu, UserRound } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage<PageType>();

const { t } = useI18n();

const menufilters = computed(() => page.props.menu);
const cartProductCount = computed(() => page.props.cartProductCount);

interface MenuInterface {
    key: string;
    title: string;
    url: string;
    items: MenuItem[];
}

const genderItems: MenuItem[] = [
    { id: -1, slug: 'boy', name: t('home.boy') },
    { id: -2, slug: 'girl', name: t('home.girl') },
    { id: -3, slug: 'unisex', name: t('home.unisex') },
];

const menu = computed<MenuInterface[]>(() => [
    {
        key: 'categories',
        title: t('home.categories'),
        url: 'collection.category',
        items: menufilters.value.categories.data,
    },
    {
        key: 'marks',
        title: t('home.marks'),
        url: 'collection.marks',
        items: menufilters.value.brands.data,
    },
    {
        key: 'seasons',
        title: t('home.seasons'),
        url: 'collection.season',
        items: menufilters.value.seasons.data,
    },
    {
        key: 'gender',
        title: t('home.gender'),
        url: 'collection.gender',
        items: genderItems,
    },
]);

const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

const activeItemStyles = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        isCurrentRoute.value(toUrl(url))
            ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100'
            : '',
);

const mainNavItems: NavItem[] = [
    {
        title: t('home.home'),
        href: route('home'),
        // icon: LayoutGrid,
    },
];
</script>

<template>
    <header
        class="sticky top-0 z-40 w-full border-b border-border/70 bg-background/85 backdrop-blur-sm"
    >
        <div class="mx-auto flex h-18 w-full max-w-7xl items-center px-4 sm:px-6">
            <!-- Mobile Menu -->
            <div class="lg:hidden">
                <Sheet>
                    <SheetTrigger :as-child="true">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="mr-1 h-9 w-9 cursor-pointer"
                        >
                            <Menu class="h-5 w-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-75 p-6">
                        <SheetTitle class="sr-only"
                            >Navigation Menu</SheetTitle
                        >
                        <SheetHeader class="flex justify-start text-left">
                            <AppLogoIcon
                                class="size-6 fill-current text-foreground"
                            />
                        </SheetHeader>
                        <div
                            class="flex h-full flex-1 flex-col justify-between space-y-4 py-6"
                        >
                            <nav class="-mx-3 space-y-1">
                                <Link
                                    v-for="item in mainNavItems"
                                    :key="item.title"
                                    :href="item.href"
                                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                    :class="activeItemStyles(item.href)"
                                >
                                    <component
                                        v-if="item.icon"
                                        :is="item.icon"
                                        class="h-5 w-5"
                                    />
                                    {{ item.title }}
                                </Link>
                            </nav>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>

            <Link
                :href="route('home')"
                class="flex shrink-0 items-center gap-x-2"
            >
                <AppLogo />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden h-full flex-1 justify-center lg:flex">
                <NavigationMenu class="flex h-full items-stretch">
                    <NavigationMenuList
                        class="flex h-full items-stretch gap-x-1"
                    >
                        <!-- Main nav items -->
                        <NavigationMenuItem
                            v-for="(item, index) in mainNavItems"
                            :key="index"
                            class="relative flex h-full items-center"
                        >
                            <Link
                                :class="[
                                    navigationMenuTriggerStyle(),
                                    activeItemStyles(item.href),
                                    'h-9 cursor-pointer bg-transparent px-3 font-medium',
                                ]"
                                :href="item.href"
                            >
                                <component
                                    v-if="item.icon"
                                    :is="item.icon"
                                    class="mr-2 h-4 w-4"
                                />
                                {{ item.title }}
                            </Link>

                            <div
                                v-if="isCurrentRoute(item.href)"
                                class="absolute bottom-1 left-3 h-0.5 w-[calc(100%-1.5rem)] rounded-full bg-foreground"
                            ></div>
                        </NavigationMenuItem>

                        <NavigationMenuItem
                            v-for="menuItem in menu"
                            :key="menuItem.key"
                            class="relative flex h-full items-center"
                        >
                            <NavigationMenuTrigger
                                class="bg-transparent font-medium"
                                >{{ menuItem.title }}</NavigationMenuTrigger
                            >
                            <NavigationMenuContent>
                                <ul class="grid w-50 gap-1 p-2">
                                    <li v-for="item in menuItem.items" :key="item.slug">
                                        <NavigationMenuLink as-child>
                                            <a
                                                :href="
                                                    route(
                                                        menuItem.url,
                                                        item.slug,
                                                    )
                                                "
                                                class="block rounded-md px-2 py-1.5 text-sm transition-colors hover:bg-accent"
                                                >{{ item.name }}</a
                                            >
                                        </NavigationMenuLink>
                                    </li>
                                </ul>
                            </NavigationMenuContent>
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>

            <div class="ml-auto flex items-center gap-1">
                <Link
                    key="cart"
                    :href="route('cart.index')"
                    class="relative flex items-center rounded-full p-2.5 transition-colors hover:bg-accent"
                    :class="activeItemStyles(route('cart.index'))"
                >
                    <ShoppingBag class="h-5 w-5" />

                    <span
                        v-if="cartProductCount > 0"
                        class="absolute top-0.5 right-0.5 flex h-4.5 min-w-4.5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
                    >
                        {{ cartProductCount }}
                    </span>
                </Link>
                <DropdownMenu v-if="auth.user">
                    <DropdownMenuTrigger :as-child="true">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative size-10 w-auto cursor-pointer rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                        >
                            <Avatar
                                class="size-8 overflow-hidden rounded-full"
                            >
                                <AvatarImage
                                    v-if="auth.user.avatar"
                                    :src="auth.user.avatar"
                                    :alt="auth.user.name"
                                />
                                <AvatarFallback
                                    class="rounded-full bg-secondary font-semibold text-secondary-foreground"
                                >
                                    {{ getInitials(auth.user?.name) }}
                                </AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <UserMenuContent :user="auth.user" />
                    </DropdownMenuContent>
                </DropdownMenu>
                <Link
                    v-else
                    key="login"
                    :href="route('login')"
                    class="flex items-center rounded-full p-2.5 transition-colors hover:bg-accent"
                    :class="activeItemStyles(route('login'))"
                >
                    <UserRound class="h-5 w-5" />
                </Link>
            </div>
        </div>
    </header>
</template>
