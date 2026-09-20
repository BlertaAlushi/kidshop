import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
    badge?: string | number;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    admin:boolean;
}

export interface Product {
    id: number;
    name: string;
    slug:string;
    description:string;
    price: number;
    currency: string;
    stock_quantity: number;
    mark:string;
    image:string,
}

export interface CartProduct {
    id: number;
    user_id: number;
    product_id: number;
    name:string;
    price:number;
    quantity:number;
    currency:string;
    image:string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Filters {
    skin_types: number[];
    skin_concerns: number[];
    product_types: number[];
    extras: number[];
    per_page: string | null;
    order_by: string | null;
    search:string | '';
}

export interface MenuItem {
    id:number
    slug: string;
    name: string;
}

export interface Language {
    id:number
    code: string;
    language: string;
}

export interface MenuType {
    bodyParts: MenuData;
    productTypes: MenuData;
    skinTypes: MenuData;
    skinConcerns: MenuData;
    extras: MenuData;
    marks: MenuData;
}

interface MenuData{
    data:MenuItem[]
}

export interface PageType extends AppPageProps{
    menu: MenuType;
    languages: Language[];
    app_domain:string;
    flash:{
        success:string| null;
    };
    cartProductCount:number;
    cartTotalPrice:number;
}

export interface Item{
    id:number;
    slug:string
    name:string;
    translations:Translation[];
}

export interface Translation{
    language_id:number,
    name:string,
    description?:string
}
export interface Mark {
    id: number;
    slug: string;
    name: string;
}

export interface ProductForm {
    id?: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    currency: string;
    stock_quantity: number;
    mark_id: number| null;
    image: string | null;
    translations: Translation[];
    body_parts: number[];
    skin_types: number[];
    skin_concerns: number[];
    product_types: number[];
    extras: number[];
}
