<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_this_months_sales(): void
    {
        $user = User::factory()->create();

        Sale::create([
            'user_id'       => $user->id,
            'customer_name' => null,
            'total_cents'   => 1250,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('12,50 €');
    }

    public function test_dashboard_works_with_no_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertOk()->assertSee('Todavía no hay ventas.');
    }
}
