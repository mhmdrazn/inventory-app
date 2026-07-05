<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    // ---------------- Product ----------------

    public function test_manager_cannot_create_product(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/products', [
            'code' => 'INV-M-001',
            'name' => 'Blocked',
            'category_id' => $category->id,
            'stock' => 3,
            'condition' => 'baik',
        ]);

        $response->assertForbidden();
        $response->assertJsonPath('success', false);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_manager_cannot_update_product(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $product = Product::factory()->create();

        $response = $this->patchJson("/api/v1/products/{$product->id}", ['name' => 'Renamed']);

        $response->assertForbidden();
        $this->assertSame($product->name, $product->fresh()->name);
    }

    public function test_manager_cannot_delete_product(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_manager_can_view_products(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk();
        $response->assertJsonPath('success', true);
    }

    // ---------------- Borrowing ----------------

    public function test_manager_cannot_create_borrowing(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $product = Product::factory()->withStock(5)->create();

        $response = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'X',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('borrowings', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_manager_cannot_return_borrowing(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $creator = $this->userWithRole('staff');
        $borrowing = Borrowing::factory()->create(['user_id' => $creator->id]);
        $product = Product::factory()->withStock(3)->create();
        BorrowingDetail::factory()->create([
            'borrowing_id' => $borrowing->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->patchJson("/api/v1/borrowings/{$borrowing->id}/return");

        $response->assertForbidden();
        $this->assertSame('dipinjam', $borrowing->fresh()->status);
    }

    public function test_manager_can_view_borrowings(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $staff = $this->userWithRole('staff');
        Borrowing::factory()->count(2)->create(['user_id' => $staff->id]);

        $response = $this->getJson('/api/v1/borrowings');

        $response->assertOk();
    }

    public function test_manager_cannot_update_borrowing(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $creator = $this->userWithRole('staff');
        $borrowing = Borrowing::factory()->create(['user_id' => $creator->id]);

        $response = $this->putJson("/api/v1/borrowings/{$borrowing->id}", ['notes' => 'Updated']);

        $response->assertForbidden();
        $response->assertJsonPath('success', false);
        $this->assertNull($borrowing->fresh()->notes);
    }

    public function test_manager_cannot_delete_borrowing(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));
        $creator = $this->userWithRole('staff');
        $borrowing = Borrowing::factory()->returned()->create(['user_id' => $creator->id]);

        $response = $this->deleteJson("/api/v1/borrowings/{$borrowing->id}");

        $response->assertForbidden();
        $response->assertJsonPath('success', false);
        $this->assertDatabaseHas('borrowings', ['id' => $borrowing->id]);
    }

    // ---------------- Category ----------------

    public function test_manager_can_view_categories_via_api(): void
    {
        Sanctum::actingAs($this->userWithRole('manager'));

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk();
    }

    public function test_staff_cannot_create_category_on_web(): void
    {
        $staff = $this->userWithRole('staff');

        $response = $this->actingAs($staff)->post(route('categories.store'), ['name' => 'New Category']);

        $response->assertForbidden();
        $this->assertDatabaseMissing('categories', ['name' => 'New Category']);
    }
}
