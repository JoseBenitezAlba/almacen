<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock, int $price = 1250): Product
    {
        return Product::create([
            'code'        => 'P-' . uniqid(),
            'name'        => 'Producto de prueba',
            'unit'        => 'ud',
            'price_cents' => $price,
            'stock'       => $stock,
            'min_stock'   => 0,
        ]);
    }

    public function test_guest_cannot_see_sales(): void
    {
        $this->get(route('sales.index'))->assertRedirect(route('login'));
    }

    public function test_employee_can_register_a_sale_and_stock_is_reduced(): void
    {
        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        $product = $this->product(stock: 10);

        $this->actingAs($employee)->post(route('sales.store'), [
            'customer_name' => 'Ana',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ])->assertRedirect();

        $sale = Sale::first();

        $this->assertSame(3750, $sale->total_cents);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type'       => 'out',
            'quantity'   => 3,
        ]);
    }

    public function test_sale_without_enough_stock_saves_nothing(): void
    {
        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        $product = $this->product(stock: 2);

        $this->actingAs($employee)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(2, $product->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_sale_keeps_the_price_it_was_made_at(): void
    {
        $employee = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        $product = $this->product(stock: 10, price: 1250);

        $this->actingAs($employee)->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $product->update(['price_cents' => 9999]);

        $this->assertSame(1250, Sale::first()->items()->first()->unit_price_cents);
    }
}
