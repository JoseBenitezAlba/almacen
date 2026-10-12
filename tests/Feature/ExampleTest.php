<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_see_the_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertOk();
    }
}