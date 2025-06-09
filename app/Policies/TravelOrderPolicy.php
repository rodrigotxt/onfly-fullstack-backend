<?php

namespace App\Policies;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TravelOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('view_any_travel_orders');
    }

    public function view(User $user, TravelOrder $travelOrder)
    {
        return $user->id === $travelOrder->user_id || $user->hasPermission('view_travel_orders');
    }

    public function create(User $user)
    {
        return true; // Todos usuários autenticados podem criar
    }

    public function updateStatus(User $user, TravelOrder $travelOrder)
    {
        // Apenas administradores podem alterar status
        return $user->hasPermission('update_travel_order_status') && $user->id !== $travelOrder->user_id;
    }

    public function cancel(User $user, TravelOrder $travelOrder)
    {
        // O próprio usuário pode cancelar se for aprovado
        return ($user->id === $travelOrder->user_id && $travelOrder->status === 'aprovado') 
            || $user->hasPermission('cancel_travel_orders');
    }

    public function delete(User $user, TravelOrder $travelOrder)
    {
        return $user->hasPermission('delete_travel_orders');
    }
}