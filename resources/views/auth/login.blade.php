<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Controle de Estoque CEVESP</title>
    <link rel="icon" type="image/png" href="{{ asset('logo-cevesp.png') }}">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.0/dist/tailwind.min.css" rel="stylesheet">
    @endif
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo -->
        <div class="text-center">
            <img
                src="{{ asset('logo-cevesp.png') }}"
                alt="CEVESP - Centro de Cirurgia Minimamente Invasiva"
                class="mx-auto h-24 w-auto mb-6"
            >
            <h2 class="text-3xl font-bold text-gray-900">Controle de Estoque</h2>
            <p class="mt-2 text-sm text-gray-600">Faça login para acessar o sistema</p>
        </div>

        <!-- Formulário de Login -->
        <div class="bg-white shadow-xl rounded-lg p-8">
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf

                <div>
                    <label for="usuario" class="block text-sm font-medium text-gray-700 mb-2">
                        Usuário
                    </label>
                    <input
                        id="usuario"
                        name="usuario"
                        type="text"
                        autocomplete="username"
                        required
                        value="{{ old('usuario') }}"
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('usuario') border-red-500 @enderror"
                        placeholder="Digite seu usuário"
                    >
                    @error('usuario')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror"
                        placeholder="Digite sua senha"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Lembrar-me
                        </label>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out cursor-pointer"
                    >
                        Entrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center space-y-1">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} CEVESP - Centro de Cirurgia Minimamente Invasiva
            </p>
            <p class="text-xs text-gray-400">
                Desenvolvido por <a href="https://linktr.ee/vv_bittencourt" target="_blank" rel="noopener noreferrer" class="text-yellow-500 hover:text-yellow-600 underline transition-colors">Vitor Bittencourt</a>
            </p>
        </div>
    </div>
</body>
</html>

