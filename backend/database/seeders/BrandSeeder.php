<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = ['nike', 'adidas', "levi's", 'guess', 'tommy hilfiger', 'emporio armani', 'calvin klein', 'polo ralph lauren'];

        foreach ($brands as $brand) {
            Brand::factory()->create(['name' => $brand]);
        }
    }
}
