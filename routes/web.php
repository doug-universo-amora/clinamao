<?php

use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\BloqueioAgendaController;
use App\Http\Controllers\DisponibilidadeController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\Portal\PortalAgendamentoController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalDashboardController;
use App\Http\Controllers\Portal\PortalPerfilController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfissionalController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Usuários
    Route::resource('usuarios', UsuarioController::class)->except(['show']);

    // Profissionais
    Route::resource('profissionais', ProfissionalController::class)->except(['show']);

    // Pacientes
    Route::resource('pacientes', PacienteController::class);

    // ─── Agenda Module ───────────────────────────────────────

    // Agenda principal (visualização dia/semana)
    Route::get('/agenda', [AgendamentoController::class, 'index'])->name('agenda.index');

    // Disponibilidades
    Route::resource('disponibilidades', DisponibilidadeController::class)->except(['create', 'show', 'edit']);

    // Bloqueios de agenda
    Route::resource('bloqueios', BloqueioAgendaController::class)->except(['create', 'show', 'edit']);

    // Agendamentos
    Route::get('/agendamentos/criar', [AgendamentoController::class, 'create'])->name('agendamentos.create');
    Route::post('/agendamentos', [AgendamentoController::class, 'store'])->name('agendamentos.store');
    Route::put('/agendamentos/{id}/reagendar', [AgendamentoController::class, 'reagendar'])->name('agendamentos.reagendar');
    Route::put('/agendamentos/{id}/status', [AgendamentoController::class, 'alterarStatus'])->name('agendamentos.status');
    Route::put('/agendamentos/{id}/chegada', [AgendamentoController::class, 'registrarChegada'])->name('agendamentos.chegada');
    Route::delete('/agendamentos/{id}/chegada', [AgendamentoController::class, 'desfazerChegada'])->name('agendamentos.chegada.desfazer');
    Route::delete('/agendamentos/{id}/cancelar', [AgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar');

    // API: horários disponíveis (JSON)
    Route::get('/api/horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])->name('api.horarios');
});

// ─── Portal do Paciente ──────────────────────────────────
Route::prefix('portal')->group(function () {
    // Rotas públicas (login)
    Route::get('/login', [PortalAuthController::class, 'loginForm'])->name('portal.login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('portal.login.post');
    Route::post('/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');

    // Rotas protegidas
    Route::middleware([\App\Http\Middleware\PortalPacienteMiddleware::class, \App\Http\Middleware\HandlePortalInertiaRequests::class])->group(function () {
        Route::get('/', [PortalDashboardController::class, 'index'])->name('portal.dashboard');
        Route::get('/agendamentos', [PortalAgendamentoController::class, 'index'])->name('portal.agendamentos');
        Route::get('/agendamentos/{id}', [PortalAgendamentoController::class, 'show'])->name('portal.agendamentos.show');
        Route::get('/perfil', [PortalPerfilController::class, 'edit'])->name('portal.perfil');
        Route::put('/perfil', [PortalPerfilController::class, 'update'])->name('portal.perfil.update');
        Route::put('/perfil/senha', [PortalPerfilController::class, 'alterarSenha'])->name('portal.perfil.senha');
    });
});

require __DIR__.'/auth.php';
