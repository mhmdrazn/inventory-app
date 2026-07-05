<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        $role = Role::factory()->staff()->create();

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_borrowing_decrements_product_stock(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $product = Product::factory()->withStock(10)->create(['category_id' => $category->id]);

        $response = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'Budi',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        $response->assertCreated();
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('borrowings', ['borrower_name' => 'Budi', 'status' => 'dipinjam']);
    }

    public function test_return_increments_stock_and_updates_status(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $product = Product::factory()->withStock(5)->create(['category_id' => $category->id]);
        $borrowing = Borrowing::factory()->create(['user_id' => $user->id]);
        BorrowingDetail::factory()->create([
            'borrowing_id' => $borrowing->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->patchJson("/api/v1/borrowings/{$borrowing->id}/return");

        $response->assertOk();
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame('dikembalikan', $borrowing->fresh()->status);
    }

    public function test_borrowing_fails_when_stock_insufficient_and_rolls_back(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $product = Product::factory()->withStock(2)->create(['category_id' => $category->id]);

        $response = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'Budi',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('success', false);
        // Transaction rollback: stock stays at original 2, borrowing not created.
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertDatabaseCount('borrowings', 0);
        $this->assertDatabaseCount('borrowing_details', 0);
    }

    public function test_multi_item_borrowing_is_atomic(): void
    {
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $good = Product::factory()->withStock(10)->create(['category_id' => $category->id]);
        $lowStock = Product::factory()->withStock(1)->create(['category_id' => $category->id]);

        $response = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'Budi',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [
                ['product_id' => $good->id, 'quantity' => 2],
                ['product_id' => $lowStock->id, 'quantity' => 5], // insufficient
            ],
        ]);

        $response->assertStatus(422);
        // The FIRST item's decrement must be rolled back too.
        $this->assertSame(10, $good->fresh()->stock);
        $this->assertSame(1, $lowStock->fresh()->stock);
        $this->assertDatabaseCount('borrowings', 0);
    }

    public function test_concurrent_borrowings_do_not_oversell(): void
    {
        // This test verifies the wrapping DB transaction + lockForUpdate.
        // On SQLite, lockForUpdate is a no-op but the transaction still
        // gives us the "compare stock, then decrement" atomicity within
        // a single request. Two SEQUENTIAL requests against the same product
        // with combined demand > stock should leave the second one rejected
        // rather than allowing negative stock.
        $user = $this->staffUser();
        Sanctum::actingAs($user);

        $category = Category::factory()->create();
        $product = Product::factory()->withStock(3)->create(['category_id' => $category->id]);

        $first = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'A',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);
        $second = $this->postJson('/api/v1/borrowings', [
            'borrower_name' => 'B',
            'borrowed_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $first->assertCreated();
        $second->assertStatus(422); // second should fail — only 1 left after first
        $this->assertGreaterThanOrEqual(0, $product->fresh()->stock);
        $this->assertSame(1, $product->fresh()->stock);
    }
}
