<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $previousStatus;

    public function __construct($order, $previousStatus)
    {
        $this->order = $order;
        $this->previousStatus = $previousStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Status do seu pedido de viagem foi alterado')
                    ->line("O status do seu pedido de viagem #{$this->order->order_id} foi alterado de {$this->previousStatus} para {$this->order->status}.")
                    ->action('Ver Pedido', url("/orders/{$this->order->id}"))
                    ->line('Obrigado por usar nosso serviço!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_id,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->order->status,
            'message' => "Status do pedido alterado de {$this->previousStatus} para {$this->order->status}",
        ];
    }
}