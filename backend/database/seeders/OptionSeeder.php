<?php

namespace Database\Seeders;

use App\Models\Products\Option;
use App\Models\Products\Variant;
use App\Models\Size;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = Size::pluck('id');

        Variant::each(function ($variant) use ($sizes) {
            foreach ($sizes as $size) {
                Option::factory()->create([
                    'variant_id' => $variant->id,
                    'size_id' => $size,
                ]);
            }
        });
    }
}
