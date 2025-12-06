# 🔌 Guia de Implementação de WebSockets para Notificações

## 📚 Como Funciona WebSockets

### Situação Atual (Polling)
```
Cliente → [A cada 30s] → Servidor: "Tem notificações?"
Servidor → Cliente: "Sim, aqui estão"
```

**Problemas:**
- Delay de até 30 segundos
- Requisições desnecessárias mesmo sem notificações
- Mais carga no servidor

### Com WebSockets
```
Cliente ←→ [Conexão Persistente] ←→ Servidor
Servidor → Cliente: "Nova notificação!" (instantâneo)
```

**Vantagens:**
- ✅ Notificações instantâneas
- ✅ Menos requisições HTTP
- ✅ Melhor experiência do usuário
- ✅ Economia de recursos

---

## 🚀 Implementação com Laravel Reverb

### 1. Instalação

```bash
# Instalar Reverb
composer require laravel/reverb

# Publicar configuração
php artisan reverb:install

# Instalar dependências JavaScript
npm install --save-dev laravel-echo pusher-js
```

### 2. Configuração (.env)

```env
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

BROADCAST_DRIVER=reverb
```

### 3. Criar Event de Notificação

```php
// app/Events/NotificacaoCriada.php
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

    public function broadcastOn(): array
    {
        return [
            new Channel('notificacoes'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notificacao.criada';
    }
}
```

### 4. Disparar Event ao Criar Notificação

```php
// app/Console/Commands/ChecarAlertasEstoque.php

use App\Events\NotificacaoCriada;

// Quando criar uma notificação:
$notificacao = Notificacao::create([...]);
event(new NotificacaoCriada($notificacao));
```

### 5. Frontend - Configurar Laravel Echo

```javascript
// resources/js/app.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### 6. Frontend - Escutar Notificações (Alpine.js)

```html
<!-- resources/views/layouts/app.blade.php -->
<div x-data="{
    open: false,
    notificacoes: [],
    total: 0,
    init() {
        // Carregar notificações iniciais
        this.loadNotificacoes();
        
        // Escutar novas notificações via WebSocket
        window.Echo.channel('notificacoes')
            .listen('.notificacao.criada', (e) => {
                console.log('Nova notificação recebida!', e);
                this.notificacoes.unshift(e.notificacao);
                this.total++;
                
                // Opcional: Mostrar notificação visual
                this.showNotification(e.notificacao);
            });
    },
    async loadNotificacoes() {
        // ... código existente
    },
    showNotification(notificacao) {
        // Criar toast/alert visual
        // Exemplo com biblioteca de toast
    }
}">
```

### 7. Iniciar Servidor Reverb

```bash
# Desenvolvimento
php artisan reverb:start

# Produção (com Supervisor)
# Adicionar ao supervisor para rodar em background
```

---

## 📊 Comparação: Antes vs Depois

### Antes (Polling)
- ⏱️ Delay: até 30 segundos
- 🔄 Requisições: 120 por hora por usuário
- 💾 Recursos: médio

### Depois (WebSockets)
- ⚡ Delay: instantâneo (< 1 segundo)
- 🔄 Requisições: apenas quando necessário
- 💾 Recursos: baixo (conexão persistente)

---

## 🎯 Quando Usar WebSockets?

**Use quando:**
- ✅ Notificações precisam ser instantâneas
- ✅ Múltiplos usuários precisam ver atualizações em tempo real
- ✅ Chat, notificações, dashboards ao vivo

**Não precisa quando:**
- ❌ Aplicações simples com poucos usuários
- ❌ Notificações não são críticas
- ❌ Infraestrutura limitada

---

## 🔧 Alternativas

### Pusher (Serviço Gerenciado)
- ✅ Mais fácil de configurar
- ✅ Escalável automaticamente
- ❌ Pago (plano gratuito limitado)

### Laravel WebSockets (Self-hosted)
- ✅ Gratuito
- ✅ Controle total
- ❌ Mais complexo de configurar

---

## 📝 Próximos Passos

1. Decidir qual solução usar (Reverb recomendado)
2. Instalar e configurar
3. Criar eventos de broadcast
4. Atualizar frontend para escutar
5. Testar em desenvolvimento
6. Configurar para produção

