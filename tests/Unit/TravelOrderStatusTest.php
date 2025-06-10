<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TravelOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function usuario_nao_pode_atualizar_proprio_pedido()
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado'
        ]);

        $response = $this->loginAsApiUser($user)
            ->putJson("/api/travel/order/{$order->id}/status", [
                'status' => 'aprovado'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Não é permitido atualizar o status de um pedido criado pelo mesmo usuário.'
            ]);
    }

    #[Test]
    public function cancelamento_requer_motivo()
    {
        $user = User::factory()->create();
        
        $order = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => 'solicitado'
        ]);        

        $response = $this->loginAsApiUser($user)
            ->putJson("/api/travel/order/{$order->id}/status", [
                'status' => 'cancelado'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cancel_reason']);
    }

    #[Test]
    public function atualizacao_inclui_usuario_atualizador()
    {
        $user_updater = User::factory()->create();
        $user = User::factory()->create();
        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $this->loginAsApiUser($user_updater)
            ->putJson("/api/travel/order/{$order->id}/status", [
                'status' => 'aprovado'
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $order->id,
            'updated_by' => $user_updater->id
        ]);
    }
    
    #[Test]
    public function retorna_404_se_pedido_nao_existe()
    {
        $this->loginAsApiUser();

        $response = $this->putJson("/api/travel/order/999/status", [
            'status' => 'aprovado'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Pedido de viagem não encontrado.'
            ]);
    }
}