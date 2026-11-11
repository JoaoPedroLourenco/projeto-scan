<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockBatch;
use App\Enums\UserRole;

class StockTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_access_stock_entry_endpoint(): void
    {
        $user = User::factory()->create(['role' => UserRole::customer]);

        $this->actingAs($user);

        $response = $this->postJson('/api/stock/entry', []);

        $response->assertForbidden();
    }

    public function test_operador_stock_entry_endpoint(): void
    {
        $user = User::factory()->create(['role' => UserRole::operator]);

        $this->actingAs($user);

        $category = Category::create(['name' => 'Bebidas', 'code' => 'BEB-01']);

        $product = Product::create(['category_id' => $category->id, 'name' => 'Refrigerante', 'barcode' => '7891234567890', 'price' => 8.50]);

        $payload = [ 
            'product_id' => $product->id,
            'batch_code' => 'LOTE-2026-A',
            'quantity' => 10,
            'expiration_date' => now()->addYear()->toDateString(),
            'unit_price' => 5.50,
        ];

        $response = $this->actingAs($user)->postJson('/api/stock/entry', $payload);

        $response->assertCreated();
        $this->assertDatabaseHas('stock_batches', ['batch_code' => 'LOTE-2026-A', 'quantity' => 10]);
        $this->assertDatabaseHas('stock_movements', ['user_id' => $user->id, 'type' => 'entry', 'quantity' => 10]);
    }

    public function test_cannot_exit_more_stock_than_available_in_batch(): void
    {
        $user = User::factory()->create(['role' => UserRole::operator]);

        $this->actingAs($user);

        $category = Category::create(['name' => 'Bebidas', 'code' => 'BEB-01']);

        $product = Product::create(['category_id' => $category->id, 'name' => 'Refrigerante', 'barcode' => '7891234567890', 'price' => 8.50]);

        $batch = StockBatch::create(['product_id' => $product->id, 'batch_code' => 'LOTE-SAIDA', 'quantity' => 5, 'expiration_date' => now()->addYear()]);

        $payload = [ 
           'stock_batch_id' => $batch->id,
           'quantity' => 6,
        ];

        $response = $this->actingAs($user)->postJson('/api/stock/exit', $payload);

        $response->assertUnprocessable();
        $this->assertDatabaseHas('stock_batches', ['id' => $batch->id, 'quantity' => 5]);
        $this->assertDatabaseMissing('stock_movements', ['stock_batch_id' => $batch->id, 'type' => 'exit']);
    }
}
