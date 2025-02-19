<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\SecurityLog;

class SecurityTest extends TestCase
{
    public function test_it_logs_suspicious_activity()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
                        ->post('/login', [
                            'email' => $user->email,
                            'password' => 'wrong-password'
                        ]);
                        
        $this->assertDatabaseHas('security_logs', [
            'user_id' => $user->id,
            'action' => 'failed_login',
            'is_suspicious' => true
        ]);
    }
} 