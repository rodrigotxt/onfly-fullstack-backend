<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TravelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

class TravelOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->admin = User::factory()->create();
        $this->artisan('db:seed');

        $this->admin->guard_name = 'api';
        $this->admin->assignRole('admin');
        
        // Criar alguns pedidos para testes
        TravelOrder::factory()->count(3)->create(['user_id' => $this->user->id]);
        TravelOrder::factory()->create(['status' => 'cancelado', 'user_id' => $this->user->id]);
    }

    #[Test]
    public function usuario_pode_criar_novo_pedido()
    {
        $this->loginAsApiUser($this->user);

        $response = $this->postJson('/api/travel/orders', [
            'user_id' => $this->user->id,
            'destination' => 'Nova York',
            'customer_name' => 'Cliente Teste',
            'start_date' => fake()->date(now()->format('Y-m-d')),
            'end_date' => fake()->date(now()->addDays(5)->format('Y-m-d')),
            'status' => 'solicitado'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
            'data' =>[
                'id',
                'destination',
                'status',
                'user' => ['id', 'name']
                    ]
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'Nova York',
            'user_id' => $this->user->id
        ]);
    }

    #[Test]
    public function validacao_ao_criar_pedido()
    {
        $this->loginAsApiUser($this->user);

        $response = $this->postJson('/api/travel/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'destination',
                'customer_name',
                'start_date',
                'end_date'
            ]);
    }

    #[Test]
    public function usuario_pode_listar_seus_pedidos()
    {
        $this->loginAsApiUser($this->user);

        $response = $this->getJson('/api/travel/orders');

        $response->assertStatus(200)
            ->assertJsonCount(4, 'data') // 4 pedidos criados no setUp
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'destination',
                        'status',
                        'user' => ['id', 'name']
                    ]
                ]
            ]);
    }

    #[Test]
    public function admin_pode_listar_todos_pedidos()
    {
        $this->loginAsApiUser($this->user);

        // Criar pedido de outro usuário
        TravelOrder::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson('/api/travel/orders');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data'); // 4 do setUp + 1 novo
    }

    #[Test]
    public function usuario_pode_ver_detalhes_do_pedido()
    {
        $this->loginAsApiUser($this->user);
        $order = TravelOrder::first();

        $response = $this->getJson("/api/travel/order/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                'id' => $order->id,
                'destination' => $order->destination,
                'user' => ['id' => $this->user->id]
                ]
            ]);
    }

    #[Test]
    public function pedido_pode_ser_cancelado()
    {
        $other_user = User::factory()->create();
        $this->loginAsApiUser($other_user);
        $order = TravelOrder::first();

        $response = $this->putJson("/api/travel/order/{$order->id}/cancel", [
            'cancel_reason' => 'Motivo do cancelamento'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [                    
                    'status' => 'cancelado',
                    'cancel_reason' => 'Motivo do cancelamento'
                    ]
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'id' => $order->id,
            'status' => 'cancelado'
        ]);
    }

    #[Test]
    public function usuario_nao_pode_cancelar_pedido_ja_cancelado()
    {
        $other_user = User::factory()->create();
        $this->loginAsApiUser($other_user);

        $order = TravelOrder::first();
        $order->update(['status' => 'cancelado']);

        $response = $this->putJson("/api/travel/order/{$order->id}/cancel", [
            'cancel_reason' => 'Novo motivo'
        ]);
        
        $response->assertStatus(400)
            ->assertJson(['message' => 'Pedido já cancelado']);
    }
}