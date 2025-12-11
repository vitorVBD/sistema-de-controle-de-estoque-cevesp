import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Verificar se deve usar Pusher (produção) ou Reverb (desenvolvimento)
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;
const pusherHost = import.meta.env.VITE_PUSHER_HOST;
const pusherPort = import.meta.env.VITE_PUSHER_PORT;
const pusherScheme = import.meta.env.VITE_PUSHER_SCHEME;

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
const reverbPort = parseInt(import.meta.env.VITE_REVERB_PORT) || 8080;

// Prioridade: Pusher (produção) > Reverb (desenvolvimento)
if (pusherKey && pusherKey !== 'your-pusher-key' && pusherKey !== '') {
    // Usar Pusher para produção
    try {
        const config = {
            broadcaster: 'pusher',
            key: pusherKey,
            cluster: pusherCluster || 'mt1',
            wsHost: pusherHost,
            wsPort: pusherPort ? parseInt(pusherPort) : undefined,
            wssPort: pusherPort ? parseInt(pusherPort) : undefined,
            forceTLS: pusherScheme === 'https' || !pusherHost,
            enabledTransports: ['ws', 'wss'],
        };

        // Remover propriedades undefined
        Object.keys(config).forEach(key => {
            if (config[key] === undefined) {
                delete config[key];
            }
        });

        window.Echo = new Echo(config);

        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            pusher.connection.bind('error', (error) => {
                console.error('Erro no WebSocket (Pusher):', error);
            });
        }
    } catch (error) {
        console.error('Erro ao inicializar Laravel Echo com Pusher:', error);
        window.Echo = undefined;
    }
} else if (reverbKey && reverbHost && reverbKey !== 'your-app-key' && reverbKey !== '') {
    // Usar Reverb para desenvolvimento
    try {
        const useTLS = reverbScheme === 'https';
        const config = {
            broadcaster: 'reverb',
            key: reverbKey,
            wsHost: reverbHost,
            wsPort: reverbPort,
            wssPort: reverbPort,
            forceTLS: useTLS,
            enabledTransports: useTLS ? ['wss'] : ['ws'],
        };

        window.Echo = new Echo(config);

        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            const pusher = window.Echo.connector.pusher;
            pusher.connection.bind('error', (error) => {
                console.error('Erro no WebSocket (Reverb):', error);
            });
        }
    } catch (error) {
        console.error('Erro ao inicializar Laravel Echo com Reverb:', error);
        window.Echo = undefined;
    }
} else {
    // Nenhum WebSocket configurado - usar polling
    window.Echo = undefined;
    console.info('Laravel Echo não configurado. Notificações usarão polling.');
}

