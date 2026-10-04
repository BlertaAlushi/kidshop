<?php

namespace App\Services\Products;

use App\Models\Product;
use App\Models\Promotion;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class PromotionPricingService
{
    protected ?Collection $activePromotions = null;

    public function activePromotions(): Collection
    {
        if ($this->activePromotions !== null) {
            return $this->activePromotions;
        }

        return $this->activePromotions = Promotion::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->with('targets')
            ->get();
    }

    /**
     * Active promotions that apply to at least one target, soonest-ending first.
     */
    public function bannerPromotions(): array
    {
        return $this->activePromotions()
            ->filter(fn (Promotion $promotion) => $promotion->targets->isNotEmpty())
            ->sortBy(fn (Promotion $promotion) => $promotion->ends_at?->timestamp ?? PHP_INT_MAX)
            ->map(fn (Promotion $promotion) => $this->promotionData($promotion))
            ->values()
            ->all();
    }

    protected function promotionData(Promotion $promotion): array
    {
        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'type' => $promotion->type,
            'value' => (float) $promotion->value,
            'ends_at' => $promotion->ends_at?->toIso8601String(),
        ];
    }

    public function priceForVariant(ProductVariant $variant): array
    {
        return $this->priceFor($variant->product, $variant->color_id, (float) $variant->price);
    }

    public function priceFor(Product $product, ?int $colorId, float $price): array
    {
        $bestPromotion = null;
        $bestDiscount = 0.0;

        foreach ($this->activePromotions() as $promotion) {
            $matches = $promotion->targets->contains(function ($target) use ($product, $colorId) {
                if ($target->product_id) {
                    return $target->product_id === $product->id
                        && (!$target->color_id || $target->color_id === $colorId);
                }

                if ($target->category_id) {
                    return $target->category_id === $product->category_id;
                }

                if ($target->brand_id) {
                    return $target->brand_id === $product->brand_id;
                }

                return false;
            });

            if (!$matches) {
                continue;
            }

            $discount = $promotion->type === 'percentage'
                ? round($price * ((float) $promotion->value / 100), 2)
                : min((float) $promotion->value, $price);

            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestPromotion = $promotion;
            }
        }

        $finalPrice = round(max($price - $bestDiscount, 0), 2);

        return [
            'price' => $finalPrice,
            'original_price' => $bestPromotion ? $price : null,
            'discount_amount' => $bestPromotion ? $bestDiscount : 0.0,
            'promotion' => $bestPromotion ? $this->promotionData($bestPromotion) : null,
        ];
    }
}
