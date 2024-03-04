<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Color;
use App\Models\Products\Option;
use App\Models\Products\Product;
use App\Models\Products\Variant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $adminUser;
    private Variant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['is_admin' => false]);
        $this->adminUser = User::factory()->create(['is_admin' => true]);

        Color::factory()->create();
        Category::factory()->create();
        Brand::factory()->create();
        Size::factory()->create();
        Product::factory()->create();
        $this->variant = Variant::factory()->create([
            'published' => true,
        ]);
        Option::factory()->create();
    }

    public function test_api_returns_posts_list(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'links' => [],
            'meta' => [],
        ]);

        $response->assertJsonCount(1, 'data');
    }

    public function test_admin_can_can_access_products(): void
    {
        $this->actingAs($this->adminUser);
        $this->get('/api/admin/products')->assertStatus(200);
    }

    public function test_user_cannot_access_products(): void
    {
        $this->actingAs($this->user);
        $this->get('/api/admin/products')->assertStatus(403);
    }

    public function test_admin_can_store_product(): void
    {
        $this->actingAs($this->adminUser);

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 100,
            'category_id' => Category::firstOrCreate()->id,
            'brand_id' => Brand::firstOrCreate()->id,
        ];

        $this->post('/api/admin/products', $product)->assertStatus(201);
    }

    public function test_user_cannot_store_product(): void
    {
        $this->actingAs($this->user);

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 100,
            'category_id' => Category::firstOrCreate()->id,
            'brand_id' => Brand::firstOrCreate()->id,
        ];

        $this->post('/api/admin/products', $product)->assertStatus(403);
    }

    public function test_authenticated_user_can_add_to_cart()
    {
        $this->actingAs($this->user);

        $option = Option::first()->load('variant');

        $cartItem = [
            'option_id' => $option->id,
            'variant_id' => $option->variant->id,
        ];

        $this->post('/api/cart', $cartItem)->assertStatus(200);
        $this->get('/api/cart')->assertJsonFragment($cartItem);
    }

    public function test_authenticated_user_can_remove_from_cart()
    {
        $this->actingAs($this->user);

        $option = Option::first()->load('variant');

        $cart = Cart::create([
            'option_id' => $option->id,
            'variant_id' => $option->variant->id,
            'user_id' => $this->user->id
        ]);

        $this->delete('/api/cart/' . $cart->id)->assertStatus(200);
    }

    public function test_authenticated_user_can_add_to_wishlist()
    {
        $this->actingAs($this->user);

        $this->post('/api/favorites', ['variant_id' => $this->variant->id])->assertStatus(204);
    }

    public function test_unauthenticated_user_cannot_add_to_wishlist()
    {
        $this->post('/api/favorites', ['variant_id' => $this->variant->id])->assertStatus(302);
    }
}
