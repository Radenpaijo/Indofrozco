<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileResponsivePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_responsive_master_and_report_pages_render_successfully(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $urls = [
            '/categories',
            '/units',
            '/suppliers',
            '/warehouses',
            '/warehouse-types',
            '/locations',
            '/reports',
            '/audit-logs',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($admin)->get($url);
            $response->assertStatus(200);
        }
    }
}
