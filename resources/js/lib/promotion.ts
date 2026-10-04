import type { PromotionInfo } from '@/types';

export function formatPromotionDiscount(promotion: PromotionInfo): string {
    return promotion.type === 'percentage'
        ? `-${promotion.value}%`
        : `-${promotion.value} €`;
}

export function formatPromotionEndDate(endsAt: string): string {
    return new Date(endsAt).toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}
