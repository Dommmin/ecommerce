<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::cursor()->each(function (User $user) {
            $ratings = [];
            Product::cursor()->each(function (Product $product) use ($user, &$ratings) {
                $ratings[] = [
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'value' => random_int(1, 5),
                ];
            });
            // Split the ratings array into chunks of 20,000 records each
            $chunks = array_chunk($ratings, 20000);
            foreach ($chunks as $chunk) {
                Rating::insert($chunk);
            }
        });
    }
}
