<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $countries = [
            ['iso_2' => 'AL', 'country' => 'Albania', 'delivery_fee' => 0],
            ['iso_2' => 'XK', 'country' => 'Kosovo', 'delivery_fee' => 2],
            ['iso_2' => 'MK', 'country' => 'North Macedonia', 'delivery_fee' => 3],
            ['iso_2' => 'ME', 'country' => 'Montenegro', 'delivery_fee' => 3],
            ['iso_2' => 'RS', 'country' => 'Serbia', 'delivery_fee' => 4],
            ['iso_2' => 'GR', 'country' => 'Greece', 'delivery_fee' => 5],
            ['iso_2' => 'IT', 'country' => 'Italy', 'delivery_fee' => 6],
            ['iso_2' => 'DE', 'country' => 'Germany', 'delivery_fee' => 8],
            ['iso_2' => 'FR', 'country' => 'France', 'delivery_fee' => 8],
            ['iso_2' => 'CH', 'country' => 'Switzerland', 'delivery_fee' => 9],
            ['iso_2' => 'AT', 'country' => 'Austria', 'delivery_fee' => 8],
            ['iso_2' => 'GB', 'country' => 'United Kingdom', 'delivery_fee' => 10],
            ['iso_2' => 'BE', 'country' => 'Belgium', 'delivery_fee' => 8],
            ['iso_2' => 'NL', 'country' => 'Netherlands', 'delivery_fee' => 8],
            ['iso_2' => 'ES', 'country' => 'Spain', 'delivery_fee' => 8],
            ['iso_2' => 'SE', 'country' => 'Sweden', 'delivery_fee' => 10],
            ['iso_2' => 'US', 'country' => 'United States', 'delivery_fee' => 15],
        ];

        foreach ($countries as $country) {
            Country::query()->updateOrCreate(
                ['iso_2' => $country['iso_2']],
                $country,
            );
        }
    }
}
