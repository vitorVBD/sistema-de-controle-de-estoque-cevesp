<?php

namespace App\Events;

use App\Models\Notificacao;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificacaoCriada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notificacao;

    /**
     * Create a new event instance.
     */
    public function __construct(Notificacao $notificacao)
    {
        $this->notificacao = [
            'id' => $notificacao->id,
            'tipo_alerta' => $notificacao->tipo_alerta,
            'mensagem' => $notificacao->mensagem,
            'created_at' => $notificacao->created_at->toISOString(),
            'item' => $notificacao->item ? [
                'id' => $notificacao->item->id,
                'nome' => $notificacao->item->nome,
            ] : null,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('notificacoes'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'notificacao.criada';
    }
}
