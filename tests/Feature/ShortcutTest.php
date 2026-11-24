<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortcutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('shortcuts.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_shortcuts_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('shortcuts.index'));
        $response->assertStatus(200);
        $response->assertSee('Panduan Pintasan Keyboard');
        $response->assertSee('Enter');
        $response->assertSee('Ctrl');
    }
}
