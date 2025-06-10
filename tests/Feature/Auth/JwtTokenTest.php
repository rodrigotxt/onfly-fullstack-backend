<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;


class JwtTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_renovar_token()
    {
        $this->loginAsApiUser();

        $response = $this->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    public function test_rota_protegida_com_token_valido()
    {
        $this->loginAsApiUser();

        $user = $this->apiUser;

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email
                    ]
            ]);
    }

    public function test_rota_protegida_com_token_invalido()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer token-invalido',
            'Accept' => 'application/json'
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(401);
    }
}