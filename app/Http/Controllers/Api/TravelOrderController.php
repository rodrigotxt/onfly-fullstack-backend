<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TravelOrder;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderStatusRequest;
use App\Http\Resources\TravelOrderResource;
use App\Http\Resources\TravelOrderCollection;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TravelOrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Lista todos os pedidos de viagem com possibilidade de filtro
     *
     * @param Request $request
     * @return TravelOrderCollection
     */
    public function index(Request $request)
    {
        // Verifica se o usuário tem permissão para listar todos os pedidos
        //$this->authorize('viewAny', TravelOrder::class);

        $query = TravelOrder::query()
            ->with(['user', 'updatedBy'])
            ->orderBy('created_at', 'desc');

        // Filtro por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por destino
        if ($request->has('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        // Filtro por período (data de criação)
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        // Filtro por período (data de viagem)
        if ($request->has('travel_start_date') && $request->has('travel_end_date')) {
            $query->whereBetween('start_date', [$request->travel_start_date, $request->travel_end_date])
                  ->orWhereBetween('end_date', [$request->travel_start_date, $request->travel_end_date]);
        }

        // Paginação (opcional)
        $orders = $query->paginate($request->per_page ?? 15);

        return new TravelOrderCollection($orders);
    }

    /**
     * Cria um novo pedido de viagem
     *
     * @param StoreTravelOrderRequest $request
     * @return TravelOrderResource
     */
    public function store(StoreTravelOrderRequest $request)
    {

        $validated = $request->validated();

        $order = TravelOrder::create([
            'order_id' => $this->generateOrderId(),
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'destination' => $validated['destination'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'solicitado',
        ]);

        return new TravelOrderResource($order);
    }

    /**
     * Exibe um pedido de viagem específico
     *
     * @param $id
     * @return TravelOrderResource
     */
    public function show($id)
    {
        //$this->authorize('view', $travelOrder);
        $travelOrder = TravelOrder::with(['user', 'updatedBy'])->find($id);
        return new TravelOrderResource($travelOrder->load('user', 'updatedBy'));
    }

    /**
     * Atualiza o status de um pedido de viagem
     *
     * @param UpdateTravelOrderStatusRequest $request
     * @param $travelOrderId
     * @return TravelOrderResource
     */
    public function updateStatus(UpdateTravelOrderStatusRequest $request, $travelOrderId)
    {
        $travelOrder = TravelOrder::find($travelOrderId);

        //$this->authorize('updateStatus', $travelOrder); // TODO: controle de acesso

        if (!$travelOrder) {
            abort(404, 'Pedido de viagem nao encontrado.');
        }
        $user = Auth::user();

        // verifica se o usuário logado é o mesmo que criou o pedido
        if ($user->id == $travelOrder->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não é permitido atualizar o status de um pedido criado pelo mesmo usuário.',
            ], 403);
        }

        $previousStatus = $travelOrder->status;
        $newStatus = $request->status;

        $travelOrder->update([
            'status' => $newStatus,
            'cancel_reason' => $newStatus === 'cancelado' ? $request->cancel_reason : null
        ]);

        /*
        // Notifica o usuário sobre mudança de status
        if ($previousStatus !== $newStatus) {
            $travelOrder->user->notify(new OrderStatusChanged($travelOrder, $previousStatus));
        }
        */

        return new TravelOrderResource($travelOrder);
    }

    /**
     * Cancela um pedido de viagem aprovado
     *
     * @param Request $request
     * @param TravelOrder $travelOrder
     * @return TravelOrderResource
     */
    public function cancelApprovedOrder(Request $request, TravelOrder $travelOrder)
    {
        $this->authorize('cancel', $travelOrder);

        if ($travelOrder->status !== 'aprovado') {
            abort(400, 'Somente pedidos aprovados podem ser cancelados.');
        }

        $travelOrder->update([
            'status' => 'cancelado',
            'cancel_reason' => $request->cancel_reason
        ]);

        $travelOrder->user->notify(new OrderStatusChanged($travelOrder, 'aprovado'));

        return new TravelOrderResource($travelOrder);
    }

    /**
     * Gera um ID único para o pedido
     *
     * @return string
     */
    protected function generateOrderId()
    {
        return 'TRAVEL-' . strtoupper(uniqid());
    }
}