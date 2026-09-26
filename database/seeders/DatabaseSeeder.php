<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountrySeeder::class);

        User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test Admin',
            'email' =>fake()->unique()->safeEmail(),
            'admin' => true,
        ]);

        $categories = Category::factory(5)->create();
        $brands = Brand::factory(5)->create();

        $sizes = collect(['S', 'M', 'L', 'XL'])
            ->values()
            ->map(fn (string $name, int $order) => Size::factory()->create([
                'name' => $name,
                'sort_order' => $order,
            ]));

        $colors = collect([
            'Red' => '#FF0000',
            'Blue' => '#0000FF',
            'Green' => '#00FF00',
            'Black' => '#000000',
            'White' => '#FFFFFF',
        ])->map(fn (string $hex, string $name) => Color::factory()->create([
            'name' => $name,
            'hex_code' => $hex,
        ]));

        Product::factory(10)
            ->recycle($categories)
            ->recycle($brands)
            ->create()
            ->each(function (Product $product) use ($sizes, $colors) {
                $variantSizes = $sizes->random(min(3, $sizes->count()));
                $variantColors = $colors->random(min(2, $colors->count()));

                foreach ($variantSizes as $size) {
                    foreach ($variantColors as $color) {
                        ProductVariant::factory()->create([
                            'product_id' => $product->id,
                            'size_id' => $size->id,
                            'color_id' => $color->id,
                        ]);
                    }
                }
            });
    }
}
