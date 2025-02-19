<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SystemConfig;
use App\Models\User;

class SystemConfigTest extends TestCase
{
    public function test_admin_can_view_config_page()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
                        ->get(route('admin.config.index'));
                        
        $response->assertStatus(200);
    }

    public function test_admin_can_update_config()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
                        ->post(route('admin.config.update'), [
                            'configs' => [
                                'site_name' => 'New Name'
                            ]
                        ]);
                        
        $response->assertRedirect();
        $this->assertEquals('New Name', SystemConfig::get('site_name'));
    }
} 