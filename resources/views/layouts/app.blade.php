<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Controle de Estoque') - {{ config('app.name', 'Laravel') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!--
            ⚠️ Vite não está compilado!
            Para resolver, execute no terminal:
            1. npm install
            2. npm run build (para produção) ou npm run dev (para desenvolvimento)
        -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet">
    @endif
</head>
<body class="bg-gray-50 min-h-screen flex flex-col overflow-x-hidden">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo - Responsivo: mostra título apenas em telas maiores -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity cursor-pointer">
                        <img src="{{ asset('logo-cevesp.png') }}" alt="Logo CEVESP" class="h-10 w-auto">
                        <span class="hidden md:inline-block text-xl font-bold text-gray-900">Controle de Estoque</span>
                    </a>
                </div>

                <!-- Menu Desktop (oculto em telas menores) -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium cursor-pointer">
                        Dashboard
                    </a>
                    <a href="{{ route('itens.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium cursor-pointer">
                        Itens
                    </a>

                    @auth
                        @php
                            $user = Auth::user();
                            $isAdmin = $user && $user->role === 'administrador';
                        @endphp
                        @if($isAdmin)
                            <a href="{{ route('usuarios.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium cursor-pointer">
                                Usuários
                            </a>
                        @endif
                    @endauth

                    <!-- Toggle Dark Mode -->
                    <button onclick="toggleTheme()" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-yellow-400 px-3 py-2 rounded-md text-sm font-medium cursor-pointer transition-colors" title="Alternar modo escuro">
                        <svg id="sun-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="moon-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <!-- Notificações -->
                    <div class="relative" x-data="{
                        open: false,
                        notificacoes: [],
                        total: 0,
                        loading: true,
                        async loadNotificacoes() {
                            try {
                                this.loading = true;
                                const response = await fetch('{{ route('notificacoes.nao-lidas') }}', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Erro ao carregar notificações');
                                }

                                const data = await response.json();
                                this.notificacoes = data.notificacoes || [];
                                this.total = data.total || 0;
                            } catch (error) {
                                console.error('Erro ao carregar notificações:', error);
                                this.notificacoes = [];
                                this.total = 0;
                            } finally {
                                this.loading = false;
                            }
                        },
                        adicionarNotificacao(notificacao) {
                            // Adiciona no início da lista
                            this.notificacoes.unshift(notificacao);
                            // Limita a 5 notificações
                            if (this.notificacoes.length > 5) {
                                this.notificacoes = this.notificacoes.slice(0, 5);
                            }
                            this.total++;

                            // Opcional: Mostrar notificação visual/toast
                            console.log('Nova notificação recebida:', notificacao);
                        },
                        async marcarComoLida(notificacaoId) {
                            try {
                                const response = await fetch(`{{ url('/notificacoes') }}/${notificacaoId}/marcar-lida`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Erro ao marcar notificação como lida');
                                }

                                const data = await response.json();

                                // Remove a notificação da lista
                                this.notificacoes = this.notificacoes.filter(n => n.id !== notificacaoId);

                                // Atualiza o total
                                this.total = data.total || 0;
                            } catch (error) {
                                console.error('Erro ao marcar notificação como lida:', error);
                                alert('Erro ao marcar notificação como lida. Tente novamente.');
                            }
                        }
                    }" x-init="
                        // Carregar notificações iniciais
                        loadNotificacoes();

                        // Escutar WebSockets se Echo estiver disponível
                        if (typeof window.Echo !== 'undefined') {
                            console.log('Conectando ao WebSocket...');
                            window.Echo.channel('notificacoes')
                                .listen('.notificacao.criada', (e) => {
                                    console.log('Nova notificação via WebSocket:', e);
                                    this.adicionarNotificacao(e.notificacao);
                                });
                        } else {
                            console.warn('Laravel Echo não disponível, usando polling');
                            // Fallback para polling se WebSocket não estiver disponível
                            setInterval(() => loadNotificacoes(), 30000);
                        }
                    ">
                        <button @click="open = !open" class="relative text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="total > 0" x-text="total" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full"></span>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-900">Notificações</h3>
                                <a href="{{ route('notificacoes.index') }}" class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer">Ver todas</a>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        Carregando...
                                    </div>
                                </template>
                                <template x-if="!loading && notificacoes.length === 0">
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        Nenhuma notificação não lida
                                    </div>
                                </template>
                                <template x-if="!loading && notificacoes.length > 0">
                                    <template x-for="notificacao in notificacoes" :key="notificacao.id">
                                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span x-text="notificacao.tipo_alerta === 'validade' ? '📅' : notificacao.tipo_alerta === 'estoque_minimo' ? '⚠️' : '📊'" class="text-sm"></span>
                                                        <span class="text-xs font-semibold text-gray-600" x-text="notificacao.tipo_alerta === 'validade' ? 'Validade' : notificacao.tipo_alerta === 'estoque_minimo' ? 'Estoque Mínimo' : 'Sugestão MMC'"></span>
                                                    </div>
                                                    <p class="text-sm text-gray-800" x-text="notificacao.mensagem"></p>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="new Date(notificacao.created_at).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })"></p>
                                                </div>
                                                <button @click="marcarComoLida(notificacao.id)" class="ml-2 p-1 text-gray-400 hover:text-blue-600 transition-colors cursor-pointer" title="Marcar como lida">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 border-l border-gray-300 pl-4">
                        <span class="text-sm text-gray-600">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium cursor-pointer">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Menu Mobile (visível apenas em telas menores) -->
                <div class="md:hidden flex items-center space-x-2">
                    <!-- Modo Claro/Escuro -->
                    <button onclick="toggleTheme()" class="text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-yellow-400 p-2 rounded-md cursor-pointer transition-colors" title="Alternar modo escuro">
                        <svg id="sun-icon-mobile" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <svg id="moon-icon-mobile" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                    </button>

                    <!-- Notificações Mobile -->
                    <div class="relative" x-data="{
                        open: false,
                        notificacoes: [],
                        total: 0,
                        loading: true,
                        async loadNotificacoes() {
                            try {
                                this.loading = true;
                                const response = await fetch('{{ route('notificacoes.nao-lidas') }}', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Erro ao carregar notificações');
                                }

                                const data = await response.json();
                                this.notificacoes = data.notificacoes || [];
                                this.total = data.total || 0;
                            } catch (error) {
                                console.error('Erro ao carregar notificações:', error);
                                this.notificacoes = [];
                                this.total = 0;
                            } finally {
                                this.loading = false;
                            }
                        },
                        adicionarNotificacao(notificacao) {
                            this.notificacoes.unshift(notificacao);
                            if (this.notificacoes.length > 5) {
                                this.notificacoes = this.notificacoes.slice(0, 5);
                            }
                            this.total++;
                        },
                        async marcarComoLida(notificacaoId) {
                            try {
                                const response = await fetch(`{{ url('/notificacoes') }}/${notificacaoId}/marcar-lida`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                    }
                                });

                                if (!response.ok) {
                                    throw new Error('Erro ao marcar notificação como lida');
                                }

                                const data = await response.json();

                                // Remove a notificação da lista
                                this.notificacoes = this.notificacoes.filter(n => n.id !== notificacaoId);

                                // Atualiza o total
                                this.total = data.total || 0;
                            } catch (error) {
                                console.error('Erro ao marcar notificação como lida:', error);
                                alert('Erro ao marcar notificação como lida. Tente novamente.');
                            }
                        }
                    }" x-init="
                        loadNotificacoes();
                        if (typeof window.Echo !== 'undefined') {
                            window.Echo.channel('notificacoes')
                                .listen('.notificacao.criada', (e) => {
                                    this.adicionarNotificacao(e.notificacao);
                                });
                        } else {
                            setInterval(() => loadNotificacoes(), 30000);
                        }
                    ">
                        <button @click="open = !open" class="relative text-gray-700 hover:text-blue-600 p-2 rounded-md cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span x-show="total > 0" x-text="total" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full"></span>
                        </button>

                        <!-- Dropdown Mobile -->
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-gray-900">Notificações</h3>
                                <a href="{{ route('notificacoes.index') }}" class="text-xs text-blue-600 hover:text-blue-800 cursor-pointer">Ver todas</a>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="loading">
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        Carregando...
                                    </div>
                                </template>
                                <template x-if="!loading && notificacoes.length === 0">
                                    <div class="p-4 text-center text-sm text-gray-500">
                                        Nenhuma notificação não lida
                                    </div>
                                </template>
                                <template x-if="!loading && notificacoes.length > 0">
                                    <template x-for="notificacao in notificacoes" :key="notificacao.id">
                                        <div class="p-3 border-b border-gray-100 hover:bg-gray-50">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span x-text="notificacao.tipo_alerta === 'validade' ? '📅' : notificacao.tipo_alerta === 'estoque_minimo' ? '⚠️' : '📊'" class="text-sm"></span>
                                                        <span class="text-xs font-semibold text-gray-600" x-text="notificacao.tipo_alerta === 'validade' ? 'Validade' : notificacao.tipo_alerta === 'estoque_minimo' ? 'Estoque Mínimo' : 'Sugestão MMC'"></span>
                                                    </div>
                                                    <p class="text-sm text-gray-800" x-text="notificacao.mensagem"></p>
                                                    <p class="text-xs text-gray-500 mt-1" x-text="new Date(notificacao.created_at).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })"></p>
                                                </div>
                                                <button @click="marcarComoLida(notificacao.id)" class="ml-2 p-1 text-gray-400 hover:text-blue-600 transition-colors cursor-pointer" title="Marcar como lida">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Hambúrguer -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-700 hover:text-blue-600 p-2 rounded-md cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu Mobile -->
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                    Dashboard
                                </a>
                                <a href="{{ route('itens.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                    Itens
                                </a>
                                @auth
                                    @php
                                        $user = Auth::user();
                                        $isAdmin = $user && $user->role === 'administrador';
                                    @endphp
                                    @if($isAdmin)
                                        <a href="{{ route('usuarios.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer">
                                            Usuários
                                        </a>
                                    @endif
                                @endauth
                                <div class="border-t border-gray-200 my-1"></div>
                                <div class="px-4 py-2 text-xs text-gray-500">
                                    {{ Auth::user()->name ?? Auth::user()->email }}
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 cursor-pointer">
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 flex-1 overflow-x-hidden">
        @yield('content')
    </main>

    @yield('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Footer -->
    <footer class="mt-auto py-4 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-1">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} CEVESP - Centro de Cirurgia Minimamente Invasiva
                </p>
                <p class="text-xs text-gray-400">
                    Desenvolvido por <a href="https://linktr.ee/vv_bittencourt" target="_blank" rel="noopener noreferrer" class="text-yellow-500 hover:text-yellow-600 underline transition-colors">Vitor Bittencourt</a>
                </p>
            </div>
        </div>
    </footer>
    <style>
        [x-cloak] { display: none !important; }

        /* Prevenir scroll horizontal na página */
        html, body {
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Dark Mode Styles */
        body.dark-mode {
            background-color: #181b1d;
            color: #e5e7eb;
        }

        body.dark-mode .bg-white {
            background-color: #181b1d !important;
        }

        body.dark-mode .bg-gray-50 {
            background-color: #181b1d !important;
        }

        body.dark-mode .text-gray-900 {
            color: #e5e7eb !important;
        }

        body.dark-mode .text-gray-700 {
            color: #d1d5db !important;
        }

        body.dark-mode .text-gray-600 {
            color: #9ca3af !important;
        }

        body.dark-mode .text-gray-500 {
            color: #6b7280 !important;
        }

        body.dark-mode .border-gray-200 {
            border-color: #374151 !important;
        }

        body.dark-mode .border-gray-300 {
            border-color: #4b5563 !important;
        }

        body.dark-mode .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
        }

        body.dark-mode .bg-gray-100 {
            background-color: #1f2937 !important;
        }

        body.dark-mode .bg-gray-200 {
            background-color: #374151 !important;
        }

        body.dark-mode .hover\:bg-gray-50:hover {
            background-color: #1f2937 !important;
        }

        body.dark-mode .hover\:bg-gray-100:hover {
            background-color: #374151 !important;
        }

        /* Status badges - manter cores originais no dark mode */
        body.dark-mode .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.25) !important;
        }

        body.dark-mode .bg-red-100 {
            background-color: rgba(239, 68, 68, 0.25) !important;
        }

        body.dark-mode .bg-yellow-100 {
            background-color: rgba(234, 179, 8, 0.25) !important;
        }

        /* Cards e containers - apenas modo claro (sem borda dourada) */
        body.dark-mode .bg-white.shadow-sm {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode .bg-white.shadow-sm.rounded-lg {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode .bg-white.rounded-lg {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode .bg-white.overflow-hidden.shadow-sm.rounded-lg {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode div.bg-white.shadow-sm.rounded-lg {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode a.bg-white.overflow-hidden.shadow-sm.rounded-lg {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        /* Cards do Dashboard - com borda dourada */
        body.dark-mode .dashboard-card {
            border: 1px solid #eab543 !important;
        }

        /* Hover dourado nos cards do Dashboard */
        body.dark-mode .dashboard-card:hover {
            border-color: #eab543 !important;
            box-shadow: 0 4px 6px -1px rgba(234, 181, 67, 0.3), 0 2px 4px -1px rgba(234, 181, 67, 0.2) !important;
        }

        body.dark-mode a.dashboard-card:hover {
            border-color: #eab543 !important;
            box-shadow: 0 4px 6px -1px rgba(234, 181, 67, 0.3), 0 2px 4px -1px rgba(234, 181, 67, 0.2) !important;
        }

        body.dark-mode input[type="text"],
        body.dark-mode input[type="number"],
        body.dark-mode input[type="date"],
        body.dark-mode textarea,
        body.dark-mode select {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }

        body.dark-mode input[readonly],
        body.dark-mode input[readonly].bg-gray-50 {
            background-color: #1f2937 !important;
            color: #9ca3af !important;
        }

        /* Tabelas */
        body.dark-mode table thead {
            background-color: #1f2937 !important;
        }

        body.dark-mode table tbody tr {
            background-color: #181b1d !important;
        }

        body.dark-mode table tbody tr:hover {
            background-color: #1f2937 !important;
        }

        body.dark-mode .divide-gray-200 > * + * {
            border-color: #374151 !important;
        }

        /* Modais */
        body.dark-mode .bg-white.rounded-md {
            background-color: #1f2937 !important;
            border: 1px solid #eab543 !important;
        }

        body.dark-mode .bg-gray-50.border {
            background-color: #1f2937 !important;
            border: 1px solid #eab543 !important;
        }


        /* Links e hover */
        body.dark-mode a.text-gray-700,
        body.dark-mode .text-gray-700 {
            color: #d1d5db !important;
        }

        body.dark-mode a:hover.text-blue-600,
        body.dark-mode .hover\:text-blue-600:hover {
            color: #eab543 !important;
        }

        /* Navbar */
        body.dark-mode nav {
            background-color: #181b1d !important;
            border-color: #374151 !important;
        }

        body.dark-mode nav .text-gray-900 {
            color: #e5e7eb !important;
        }

        /* Botões */
        body.dark-mode button.text-gray-700 {
            color: #d1d5db !important;
        }

        body.dark-mode button.hover\:text-red-600:hover {
            color: #eab543 !important;
        }

        /* Manter botões de cancelar dourados no modo escuro */
        body.dark-mode .bg-yellow-600,
        body.dark-mode button.bg-yellow-600,
        body.dark-mode a.bg-yellow-600 {
            background-color: #eab543 !important;
        }

        body.dark-mode .hover\:bg-yellow-700:hover,
        body.dark-mode button.hover\:bg-yellow-700:hover,
        body.dark-mode a.hover\:bg-yellow-700:hover {
            background-color: #d4a017 !important;
        }

        /* Dropdown de notificações */
        body.dark-mode .bg-white.rounded-md.shadow-lg {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        body.dark-mode .border-gray-200 {
            border-color: #374151 !important;
        }

        body.dark-mode .text-gray-900 {
            color: #e5e7eb !important;
        }

        body.dark-mode .text-gray-800 {
            color: #d1d5db !important;
        }

        body.dark-mode .text-blue-600 {
            color: #eab543 !important;
        }

        body.dark-mode .text-blue-800 {
            color: #eab543 !important;
        }

        body.dark-mode .hover\:bg-gray-50:hover {
            background-color: #1f2937 !important;
        }

        /* Cards de ações rápidas - manter cores de status no modo escuro */
        body.dark-mode .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.15) !important;
        }

        body.dark-mode .border-green-200 {
            border-color: rgba(34, 197, 94, 0.4) !important;
        }

        body.dark-mode .hover\:bg-green-100:hover {
            background-color: rgba(34, 197, 94, 0.25) !important;
        }

        body.dark-mode .text-green-700 {
            color: #4ade80 !important;
        }

        body.dark-mode .text-green-800 {
            color: #4ade80 !important;
        }

        body.dark-mode .text-green-900 {
            color: #4ade80 !important;
        }

        body.dark-mode .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.15) !important;
        }

        body.dark-mode .border-red-200 {
            border-color: rgba(239, 68, 68, 0.4) !important;
        }

        body.dark-mode .hover\:bg-red-100:hover {
            background-color: rgba(239, 68, 68, 0.25) !important;
        }

        body.dark-mode .text-red-700 {
            color: #f87171 !important;
        }

        body.dark-mode .text-red-800 {
            color: #f87171 !important;
        }

        body.dark-mode .text-red-900 {
            color: #f87171 !important;
        }

        body.dark-mode .bg-yellow-50 {
            background-color: rgba(234, 179, 8, 0.15) !important;
        }

        body.dark-mode .border-yellow-200 {
            border-color: rgba(234, 179, 8, 0.4) !important;
        }

        body.dark-mode .border-yellow-500 {
            border-color: rgba(234, 179, 8, 0.6) !important;
        }

        body.dark-mode .hover\:bg-yellow-100:hover {
            background-color: rgba(234, 179, 8, 0.25) !important;
        }

        body.dark-mode .text-yellow-700 {
            color: #fbbf24 !important;
        }

        body.dark-mode .text-yellow-800 {
            color: #fbbf24 !important;
        }

        body.dark-mode .text-yellow-900 {
            color: #fbbf24 !important;
        }

        body.dark-mode .border-red-500 {
            border-color: rgba(239, 68, 68, 0.6) !important;
        }

        body.dark-mode .border-green-500 {
            border-color: rgba(34, 197, 94, 0.6) !important;
        }

        /* Mensagens de alerta (mantém dourado apenas para mensagens) */
        body.dark-mode .mb-4.bg-green-50 {
            background-color: rgba(234, 181, 67, 0.1) !important;
            border-color: rgba(234, 181, 67, 0.3) !important;
        }

        body.dark-mode .mb-4.bg-red-50 {
            background-color: rgba(234, 181, 67, 0.1) !important;
            border-color: rgba(234, 181, 67, 0.3) !important;
        }

        body.dark-mode .mb-4.text-green-800 {
            color: #eab543 !important;
        }

        body.dark-mode .mb-4.text-red-800 {
            color: #eab543 !important;
        }

        /* Inputs focus */
        body.dark-mode input:focus,
        body.dark-mode textarea:focus,
        body.dark-mode select:focus {
            border-color: #eab543 !important;
            outline-color: #eab543 !important;
        }

        body.dark-mode .focus\:ring-blue-500:focus {
            --tw-ring-color: #eab543 !important;
        }

        body.dark-mode .focus\:ring-green-500:focus {
            --tw-ring-color: #eab543 !important;
        }

        body.dark-mode .focus\:ring-red-500:focus {
            --tw-ring-color: #eab543 !important;
        }

        /* Placeholder */
        body.dark-mode input::placeholder,
        body.dark-mode textarea::placeholder {
            color: #6b7280 !important;
        }
    </style>

    <script>
        // Gerenciamento do tema
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme') || 'light';
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');
            const sunIconMobile = document.getElementById('sun-icon-mobile');
            const moonIconMobile = document.getElementById('moon-icon-mobile');

            function updateIcons(isDark) {
                // Desktop icons
                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
                // Mobile icons
                if (sunIconMobile && moonIconMobile) {
                    if (isDark) {
                        sunIconMobile.classList.remove('hidden');
                        moonIconMobile.classList.add('hidden');
                    } else {
                        sunIconMobile.classList.add('hidden');
                        moonIconMobile.classList.remove('hidden');
                    }
                }
            }

            if (theme === 'dark') {
                document.body.classList.add('dark-mode');
                updateIcons(true);
            } else {
                updateIcons(false);
            }

            window.toggleTheme = function() {
                document.body.classList.toggle('dark-mode');
                const isDark = document.body.classList.contains('dark-mode');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateIcons(isDark);
            };
        });
    </script>
</body>
</html>
