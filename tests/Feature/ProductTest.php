<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $role = Role::factory()->admin()->create();

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_stock_cannot_be_negative(): void
    {
        Sanctum::actingAs($this->admin());
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/products', [
            'code' => 'INV-TST-001',
            'name' => 'Test Product',
            'category_id' => $category->id,
            'stock' => -5,
            'condition' => 'baik',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_code_must_be_unique(): void
    {
        Sanctum::actingAs($this->admin());
        $category = Category::factory()->create();
        Product::factory()->create(['code' => 'INV-DUP-001', 'category_id' => $category->id]);

        $response = $this->postJson('/api/v1/products', [
            'code' => 'INV-DUP-001',
            'name' => 'Duplicate Product',
            'category_id' => $category->id,
            'stock' => 5,
            'condition' => 'baik',
        ]);

        $response->assertStatus(422);
        $this->assertSame(1, Product::where('code', 'INV-DUP-001')->count());
    }

    public function test_admin_can_create_product(): void
    {
        Sanctum::actingAs($this->admin());
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/products', [
            'code' => 'INV-OK-001',
            'name' => 'Good Product',
            'category_id' => $category->id,
            'stock' => 10,
            'condition' => 'baik',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('products', ['code' => 'INV-OK-001', 'stock' => 10]);
    }

    public function test_api_error_envelope_matches_spec_on_validation_failure(): void
    {
        Sanctum::actingAs($this->admin());

        $response = $this->postJson('/api/v1/products', []);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => ['errors'],
        ]);
        $response->assertJsonPath('success', false);
    }
}
