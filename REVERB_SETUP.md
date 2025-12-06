# 🚀 Configuração do Laravel Reverb

## 1. Variáveis de Ambiente (.env)

Adicione estas variáveis ao seu arquivo `.env`:

```env
# Reverb Configuration
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Broadcasting
BROADCAST_CONNECTION=reverb

# Vite (para frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 2. Gerar Chaves do Reverb

Execute o comando para gerar as chaves automaticamente:

```bash
php artisan reverb:install
```

Ou gere manualmente:

```bash
php artisan reverb:generate-app-id
```

## 3. Iniciar o Servidor Reverb

### Desenvolvimento

```bash
php artisan reverb:start
```

### Produção (com Supervisor)

Crie um arquivo de configuração do Supervisor em `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /caminho/para/seu/projeto/artisan reverb:start
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/caminho/para/seu/projeto/storage/logs/reverb.log
stopwaitsecs=3600
```

## 4. Compilar Assets

```bash
npm run build
# ou para desenvolvimento
npm run dev
```

## 5. Testar

1. Inicie o servidor Reverb: `php artisan reverb:start`
2. Inicie o Laravel: `php artisan serve`
3. Abra o navegador e verifique o console para ver a conexão WebSocket
4. Execute o comando de alertas: `php artisan estoque:checar-alertas`
5. A notificação deve aparecer instantaneamente no navegador!

## 6. Verificar Conexão

Abra o console do navegador (F12) e você deve ver:
- "Conectando ao WebSocket..."
- "Nova notificação via WebSocket: ..." quando uma notificação for criada

## Troubleshooting

### WebSocket não conecta
- Verifique se o servidor Reverb está rodando
- Verifique as variáveis de ambiente
- Verifique se a porta 8080 está disponível

### Notificações não aparecem
- Verifique o console do navegador para erros
- Verifique se o evento está sendo disparado: `event(new NotificacaoCriada($notificacao))`
- Verifique se `BROADCAST_CONNECTION=reverb` no .env

