<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected $apiUser;
    protected $apiToken;

    /**
     * Autentica um usuário via JWT para testes de API.
     * Opcionalmente, pode-se passar um usuário existente.
     *
     * @param \App\Models\User|null $user
     * @return $this
     */
    protected function loginAsApiUser(?User $user = null)
    {
        // Se nenhum usuário for fornecido, crie um novo
        $this->apiUser = $user ?? User::factory()->create();

        // Gere o token JWT para o usuário
        $this->apiToken = JWTAuth::fromUser($this->apiUser);

        // Retorne o cliente de teste com os cabeçalhos configurados
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Retorna o usuário que foi autenticado via API para o teste.
     *
     * @return \App\Models\User|null
     */
    protected function getApiUser(): ?User
    {
        return $this->apiUser;
    }

    /**
     * Retorna o token JWT gerado para o teste.
     *
     * @return string|null
     */
    protected function getApiToken(): ?string
    {
        return $this->apiToken;
    }
}
