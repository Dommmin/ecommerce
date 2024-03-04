<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Products\Option;
use App\Models\Products\Variant;
use App\Models\Size;
use Illuminate\Database\Seeder;

final class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = Size::pluck('id')->toArray();
        $variants = Variant::all();
        $timestamp = now();

        $options = [];

        foreach ($variants as $variant) {
            foreach ($sizes as $size) {
                $options[] = [
                    'variant_id' => $variant->id,
                    'size_id' => $size,
                    'quantity' => rand(1, 10),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        Option::insert($options);
    }
}
