<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductLookupTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $code = '0012345678905'): Product
    {
        return Product::create([
            'code' => $code, 'name' => 'Leche de prueba', 'unit' => 'ud',
            'stock' => 5, 'min_stock' => 1,
        ]);
    }

    public function test_guests_cannot_look_up_products(): void
    {
        $this->get(route('products.lookup', ['code' => '0012345678905']))
            ->assertRedirect(route('login'));
    }

    public function test_page_is_accessible_and_does_not_match_product_show_route(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        $this->actingAs($user)->get(route('products.lookup'))->assertOk()
            ->assertSee('Código exacto del producto')->assertSee('name="code"', false);
        $this->get(route('products.index'))->assertOk()->assertSee(route('products.lookup'));
    }

    public function test_exact_code_preserves_leading_zeroes_and_does_not_change_stock(): void
    {
        $product = $this->product();
        foreach ([User::ROLE_ADMIN, User::ROLE_EMPLOYEE] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('products.lookup', ['code' => $product->code]))
                ->assertRedirect(route('products.show', $product));
        }
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertSame(0, StockMovement::count());
    }

    public function test_partial_unknown_and_url_codes_never_select_or_create_a_product(): void
    {
        $this->product();
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        foreach (['12345678905', '00123', '%', 'https://example.invalid/item', 'NO-EXISTE'] as $code) {
            $this->actingAs($user)->get(route('products.lookup', ['code' => $code]))
                ->assertOk()->assertSee('No hay ningún producto con el código');
        }
        $this->assertSame(1, Product::count());
        $this->assertSame(0, StockMovement::count());
    }

    public function test_invalid_codes_are_rejected(): void
    {
        $user = User::factory()->create();
        foreach (['', str_repeat('A', 51), ['bad']] as $code) {
            $this->actingAs($user)->get(route('products.lookup', ['code' => $code]))
                ->assertSessionHasErrors('code');
        }
    }

    public function test_no_match_escapes_the_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('products.lookup', ['code' => '<script>alert(1)</script>']))
            ->assertOk()->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_employee_lookup_does_not_unlock_movements(): void
    {
        $product = $this->product();
        $user = User::factory()->create(['role' => User::ROLE_EMPLOYEE]);
        $this->actingAs($user)->get(route('products.show', $product))->assertOk()
            ->assertDontSee('Registrar movimiento');
        $this->get(route('movements.create', $product))->assertForbidden();
        $this->post(route('movements.store', $product), ['type' => 'in', 'quantity' => 1])->assertForbidden();
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_admin_can_continue_to_existing_movement_flow(): void
    {
        $product = $this->product();
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($user)->get(route('products.show', $product))->assertOk()
            ->assertSee(route('movements.create', $product));
        $this->post(route('movements.store', $product), [
            'type' => 'in', 'quantity' => 2, 'reason' => 'Recepción de prueba',
        ])->assertRedirect(route('products.show', $product));
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id, 'user_id' => $user->id,
            'quantity' => 2, 'reason' => 'Recepción de prueba',
        ]);
    }
}
