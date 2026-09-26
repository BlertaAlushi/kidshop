import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User | null;
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

export interface ProductColorOption {
    id: number;
    name: string;
    hex_code: string | null;
    image: string | null;
    images: string[];
}

export interface ProductVariantOption {
    id: number;
    price: number;
    stock_quantity: number;
    is_active: boolean;
    size: { id: number; name: string; sort_order: number } | null;
    color: { id: number; name: string; hex_code: string | null } | null;
}

export interface Product {
    id: number;
    name: string;
    slug:string;
    description:string | null;
    gender: 'boy' | 'girl' | 'unisex';
    price: number | null;
    stock_quantity: number;
    category: string | null;
    brand: string | null;
    image: string | null;
    default_variant: { id: number; price: number; stock_quantity: number } | null;
    colors: ProductColorOption[];
    variants: ProductVariantOption[];
}

export interface CartProduct {
    id: number;
    product_variant_id: number;
    product_slug: string;
    name:string;
    size: string | null;
    color: string | null;
    color_hex: string | null;
    price:number;
    quantity:number;
    image:string | null;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Filters {
    categories: number[];
    brands: number[];
    seasons: number[];
    colors: number[];
    sizes: number[];
    gender: string | null;
    per_page: string | null;
    order_by: string | null;
    search:string | '';
}

export interface MenuItem {
    id:number
    slug: string;
    name: string;
}

export interface MenuType {
    categories: MenuData;
    brands: MenuData;
    seasons: MenuData;
    colors: MenuData;
    sizes: MenuData;
}

interface MenuData{
    data:MenuItem[]
}

export interface PageType extends AppPageProps{
    menu: MenuType;
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
    is_active: boolean;
}

export interface Size {
    id: number;
    name: string;
    sort_order: number;
}

export interface Category {
    id: number;
    slug: string;
    name: string;
    is_active: boolean;
}

export interface Color {
    id: number;
    name: string;
    hex_code: string | null;
}

export interface Brand {
    id: number;
    slug?: string;
    name: string;
    is_active: boolean;
}

export interface Country {
    iso_2: string;
    country: string;
    delivery_fee: number;
}

export interface Season {
    id: number;
    slug?: string;
    name: string;
    is_active: boolean;
}

export interface AdminProductVariant {
    id?: number;
    size_id: number | null;
    color_id: number | null;
    sku: string;
    price: number;
    stock_quantity: number;
    is_active: boolean;
    size?: Size;
    color?: Color;
}

export interface AdminProductImage {
    id: number;
    color_id: number | null;
    path: string;
    sort_order: number;
    is_primary: boolean;
    color?: Color;
}

export interface PromotionTargetForm {
    id?: number;
    type: 'product' | 'category' | 'brand';
    target_id: number | null;
    color_id: number | null;
}

export interface Promotion {
    id: number;
    name: string;
    type: 'percentage' | 'fixed';
    value: number;
    starts_at: string | null;
    ends_at: string | null;
    is_active: boolean;
    targets?: PromotionTargetForm[];
    targets_count?: number;
}

export interface AdminProduct {
    id: number;
    category_id: number;
    brand_id: number;
    name: string;
    slug: string;
    description: string | null;
    gender: 'boy' | 'girl' | 'unisex';
    is_active: boolean;
    category?: Category;
    brand?: Brand;
    seasons?: Season[];
    variants?: AdminProductVariant[];
    images?: AdminProductImage[];
    variants_count?: number;
}
