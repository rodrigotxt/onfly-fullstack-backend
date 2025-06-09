<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * Lista todas as notificações do usuário autenticado
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = Auth::user()
            ->notifications()
            ->when($request->has('unread'), function ($query) use ($request) {
                return $query->where('read_at', null);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return NotificationResource::collection($notifications);
    }

    /**
     * Marca uma notificação como lida
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notificação marcada como lida',
            'notification' => new NotificationResource($notification)
        ]);
    }

    /**
     * Marca todas as notificações como lidas
     *
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()
            ->unreadNotifications
            ->markAsRead();

        return response()->json([
            'message' => 'Todas as notificações foram marcadas como lidas'
        ]);
    }

    /**
     * Obtém o número de notificações não lidas
     *
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        $count = Auth::user()
            ->unreadNotifications()
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Remove uma notificação
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'message' => 'Notificação removida com sucesso'
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove todas as notificações
     *
     * @return JsonResponse
     */
    public function destroyAll(): JsonResponse
    {
        Auth::user()
            ->notifications()
            ->delete();

        return response()->json([
            'message' => 'Todas as notificações foram removidas'
        ], Response::HTTP_NO_CONTENT);
    }
}