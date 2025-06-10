<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_fazer_logout()
    {
        $this->loginAsApiUser();

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso'
            ]);
    }

    public function test_logout_falha_sem_autenticacao()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}