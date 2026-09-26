<?php

namespace App\Services;

use App\Interfaces\Services\LookupInterface;
use App\Models\Promotion;

class PromotionsService extends LookupBaseService implements LookupInterface
{
    public function __construct()
    {
        $this->model = Promotion::class;
    }

    public function index()
    {
        return $this->model::query()
            ->withCount('targets')
            ->orderByDesc('id')
            ->get(['id', 'name', 'type', 'value', 'starts_at', 'ends_at', 'is_active']);
    }

    public function store($data)
    {
        $promotion = $this->model::create($this->promotionAttributes($data));

        $this->syncTargets($promotion, $data);

        return $promotion;
    }

    public function update($data, $item)
    {
        $item->update($this->promotionAttributes($data));

        $this->syncTargets($item, $data);

        return $item;
    }

    protected function promotionAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'type' => $data['type'],
            'value' => $data['value'],
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];
    }

    protected function syncTargets(Promotion $promotion, array $data): void
    {
        $promotion->targets()->delete();

        foreach ($data['targets'] ?? [] as $target) {
            $promotion->targets()->create([
                'product_id' => $target['type'] === 'product' ? $target['target_id'] : null,
                'category_id' => $target['type'] === 'category' ? $target['target_id'] : null,
                'brand_id' => $target['type'] === 'brand' ? $target['target_id'] : null,
                'color_id' => $target['type'] === 'product' ? ($target['color_id'] ?? null) : null,
            ]);
        }
    }
}
