<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\UsuarioController;

// Rotas públicas
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota para renovar token CSRF
Route::get('/api/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

// Rotas protegidas (requerem autenticação)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('itens', ItemController::class)->except(['show'])->parameters([
        'itens' => 'item'
    ]);
    Route::get('/itens/estoque-baixo', [ItemController::class, 'estoqueBaixo'])->name('itens.estoque-baixo');
    Route::get('/itens/estoque-normal', [ItemController::class, 'estoqueNormal'])->name('itens.estoque-normal');
    Route::get('/itens/estoque-aproximando', [ItemController::class, 'estoqueAproximando'])->name('itens.estoque-aproximando');
    Route::get('/itens/{item}/lotes', [ItemController::class, 'lotes'])->name('itens.lotes');
    Route::post('/itens/{item}/lotes/remover-vencidos', [ItemController::class, 'removerLotesVencidos'])->name('itens.lotes.remover-vencidos');
    Route::get('/itens/{item}/movimentacoes', [ItemController::class, 'movimentacoes'])->name('itens.movimentacoes');

    Route::get('/movimentacoes', [MovimentacaoController::class, 'index'])->name('movimentacoes.index');
    Route::get('/movimentacoes/entrada', [MovimentacaoController::class, 'createEntrada'])->name('movimentacoes.create-entrada');
    Route::post('/movimentacoes/entrada', [MovimentacaoController::class, 'registrarEntrada'])->name('movimentacoes.entrada');
    Route::get('/movimentacoes/saida', [MovimentacaoController::class, 'createSaida'])->name('movimentacoes.create-saida');
    Route::post('/movimentacoes/saida', [MovimentacaoController::class, 'registrarSaida'])->name('movimentacoes.saida');

    Route::get('/notificacoes', [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::get('/notificacoes/nao-lidas', [NotificacaoController::class, 'naoLidas'])->name('notificacoes.nao-lidas');
    Route::post('/notificacoes/{notificacao}/marcar-lida', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcar-lida');
    Route::post('/notificacoes/marcar-todas-lidas', [NotificacaoController::class, 'marcarTodasComoLidas'])->name('notificacoes.marcar-todas-lidas');

    // Rotas de gerenciamento de usuários (apenas para administradores)
    Route::resource('usuarios', UsuarioController::class)->except(['show'])->parameters([
        'usuarios' => 'usuario'
    ]);

    // Rotas de relatórios PDF
    Route::get('/relatorios/estoque-critico', [PdfController::class, 'estoqueCritico'])->name('relatorios.estoque-critico');
    Route::get('/relatorios/consumo', [PdfController::class, 'consumoPeriodo'])->name('relatorios.consumo');
    Route::get('/relatorios/descarte', [PdfController::class, 'descartePerdas'])->name('relatorios.descarte');
    Route::get('/relatorios/validade', [PdfController::class, 'validadeProxima'])->name('relatorios.validade');
});
