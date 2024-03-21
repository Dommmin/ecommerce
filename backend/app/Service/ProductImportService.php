<?php

namespace App\Service;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Products\Product;
use Exception;
use Illuminate\Support\Arr;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProductImportService
{
    public static function processImport($path): void
    {
        $categories = Category::pluck('id', 'name')->toArray();
        $brands = Brand::pluck('id', 'name')->toArray();

        $products = [];
        $rows = SimpleExcelReader::create($path)->getRows();

        foreach ($rows as $rowProperties) {
            self::validateHeaders($rowProperties);
            self::validateFields($rowProperties);

            $category = Arr::get($categories, $rowProperties['category']);
            $brand = Arr::get($brands, $rowProperties['brand']);

            if (! $brand || ! $category) {
                throw new Exception('Brand or category not found');
            }

            $product = self::createProduct($category, $brand, $rowProperties);
            $products[] = $product;
        }

        Product::insert($products);
    }

    private static function validateHeaders($rowProperties): void
    {
        $expectedHeaders = ['category', 'brand', 'name', 'description', 'price'];
        $keys = array_keys($rowProperties);

        sort($keys);
        sort($expectedHeaders);

        if ($keys !== $expectedHeaders) {
            throw new Exception('Headers do not match expected headers');
        }
    }

    private static function validateFields($rowProperties): void
    {
        foreach ($rowProperties as $key => $value) {
            if (! $value) {
                throw new Exception("Empty field: {$key}");
            }
        }
    }

    private static function createProduct($category, $brand, $rowProperties): array
    {
        return [
            'category_id' => $category,
            'brand_id' => $brand,
            'name' => Arr::get($rowProperties, 'name'),
            'description' => Arr::get($rowProperties, 'description'),
            'price' => Arr::get($rowProperties, 'price'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
