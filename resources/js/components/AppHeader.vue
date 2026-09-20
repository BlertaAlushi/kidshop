<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useI18n } from 'vue-i18n';
import { Globe, ShoppingBag } from 'lucide-vue-next';
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

function changeLanguage(code: string) {
    const segments = window.location.pathname.split('/').filter(Boolean);
    segments[0] = code;
    window.location.href = '/' + segments.join('/') + window.location.search;
}

const { t } = useI18n();

const menufilters = computed(() => page.props.menu);
const cartProductCount = computed(() => page.props.cartProductCount);

interface MenuInterface {
    key: string;
    title: string;
    url: string;
    items: MenuItem[];
}

const menu: MenuInterface[] = [
    {
        key: 'body_parts',
        title: t('home.products'),
        url: 'collection.types',
        items: menufilters.value.bodyParts.data,
    },
    {
        key: 'skin_types',
        title: t('home.skin_types'),
        url: 'collection.skin.types',
        items: menufilters.value.skinTypes.data,
    },
    {
        key: 'skin_concerns',
        title: t('home.skin_concerns'),
        url: 'collection.skin.concerns',
        items: menufilters.value.skinConcerns.data,
    },
    {
        key: 'product_types',
        title: t('home.product_types'),
        url: 'collection.product.types',
        items: menufilters.value.productTypes.data,
    },
    {
        key: 'marks',
        title: t('home.marks'),
        url: 'collection.marks',
        items: menufilters.value.marks.data,
    },
];

const languages = computed(() => page.props.languages);

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
    <div>
        <div>
            <div class="flex h-20 items-center p-6">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9"
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
                                    class="size-6 fill-current text-black dark:text-white"
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

                <Link :href="route('home')" class="flex items-center gap-x-2">
                    <AppLogo />
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full justify-center lg:flex lg:flex-1">
                    <NavigationMenu class="flex h-full items-stretch">
                        <NavigationMenuList
                            class="flex h-full items-stretch space-x-6"
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
                                        'h-9 cursor-pointer px-3',
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
                                    class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white"
                                ></div>
                            </NavigationMenuItem>

                            <NavigationMenuItem
                                v-for="menuItem in menu"
                                :key="menuItem.key"
                                class="relative flex h-full items-center"
                            >
                                <NavigationMenuTrigger>{{
                                    menuItem.title
                                }}</NavigationMenuTrigger>
                                <NavigationMenuContent>
                                    <ul class="grid w-50 gap-4">
                                        <li>
                                            <NavigationMenuLink
                                                v-for="item in menuItem.items"
                                                :key="item.slug"
                                                as-child
                                            >
                                                <a
                                                    :href="
                                                        route(
                                                            menuItem.url,
                                                            item.slug,
                                                        )
                                                    "
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

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <Link
                            key="cart"
                            :href="route('cart.index')"
                            class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                            :class="activeItemStyles(route('cart.index'))"
                        >
                            <div class="relative">
                                <component :is="ShoppingBag" class="h-5 w-5" />

                                <span v-if="cartProductCount>0"
                                    class="absolute -top-2 -right-2 flex h-5 min-w-4.5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
                                >
                                    {{ cartProductCount }}
                                </span>
                            </div>
                        </Link>
                        <DropdownMenu v-if="auth.user">
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
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
                                            class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
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
                            class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                            :class="activeItemStyles(route('login'))"
                        >
                            <component :is="UserRound" class="h-5 w-5" />
                        </Link>

                        <DropdownMenu>
                            <DropdownMenuTrigger :as-child="true">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative size-10 w-auto cursor-pointer px-3"
                                >
                                    <Globe />
                                </Button>
                            </DropdownMenuTrigger>

                            <DropdownMenuContent align="end">
                                <DropdownMenuItem
                                    :as-child="true"
                                    v-for="lang in languages"
                                    :key="lang.code"
                                >
                                    <Button
                                        variant="ghost"
                                        class="relative w-full cursor-pointer justify-start px-3"
                                        @click="changeLanguage(lang.code)"
                                    >
                                        {{ lang.language }}
                                    </Button>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
