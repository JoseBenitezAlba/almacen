<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAndStockTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5): Product
    {
        return Product::create([
            'code' => 'P-001',
            'name' => 'Leche',
            'unit' => 'ud',
            'stock' => $stock,
            'min_stock' => 2,
        ]);
    }

    public function test_employee_gets_403_on_product_create(): void
    {
        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);

        $this->actingAs($employee)->get(route('products.create'))->assertForbidden();
    }

    public function test_out_movement_larger_than_stock_fails(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->product(5);

        $this->actingAs($admin)
            ->post(route('movements.store', $product), [
                'type' => StockMovement::TYPE_OUT,
                'quantity' => 10,
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_product_with_movements_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->product(5);
        $product->movements()->create([
            'user_id' => $admin->id,
            'type' => StockMovement::TYPE_IN,
            'quantity' => 1,
        ]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
