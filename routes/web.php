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

    // Configurações e Acessos
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show'])->middleware('permission:roles.listar');

    // Usuários
    Route::resource('usuarios', UsuarioController::class)->except(['show'])->middleware('permission:usuarios.listar');

    // Profissionais
    Route::resource('profissionais', ProfissionalController::class)->except(['show'])->middleware('permission:profissionais.listar');
    
    // Acessos Compartilhados de Agenda
    Route::get('/profissionais/{profissional}/acessos', [ProfissionalController::class, 'acessos'])->name('profissionais.acessos')->middleware('permission:profissionais.editar');
    Route::post('/profissionais/{profissional}/acessos', [ProfissionalController::class, 'syncAcessos'])->name('profissionais.acessos.sync')->middleware('permission:profissionais.editar');

    // Pacientes
    Route::resource('pacientes', PacienteController::class)->middleware('permission:pacientes.listar');

    // ─── Agenda Module ───────────────────────────────────────

    // Agenda principal (visualização dia/semana)
    Route::get('/agenda', [AgendamentoController::class, 'index'])->name('agenda.index')->middleware('permission:agenda.visualizar');

    // Disponibilidades
    Route::resource('disponibilidades', DisponibilidadeController::class)->except(['create', 'show', 'edit'])->middleware('permission:agenda.editar');

    // Bloqueios de agenda
    Route::resource('bloqueios', BloqueioAgendaController::class)->except(['create', 'show', 'edit'])->middleware('permission:agenda.editar');

    // Agendamentos
    Route::get('/agendamentos/criar', [AgendamentoController::class, 'create'])->name('agendamentos.create')->middleware('permission:agendamentos.criar');
    Route::post('/agendamentos', [AgendamentoController::class, 'store'])->name('agendamentos.store')->middleware('permission:agendamentos.criar');
    Route::put('/agendamentos/{id}/reagendar', [AgendamentoController::class, 'reagendar'])->name('agendamentos.reagendar')->middleware('permission:agendamentos.reagendar');
    Route::put('/agendamentos/{id}/status', [AgendamentoController::class, 'alterarStatus'])->name('agendamentos.status')->middleware('permission:agendamentos.editar');
    Route::put('/agendamentos/{id}/chegada', [AgendamentoController::class, 'registrarChegada'])->name('agendamentos.chegada')->middleware('permission:agendamentos.editar');
    Route::delete('/agendamentos/{id}/chegada', [AgendamentoController::class, 'desfazerChegada'])->name('agendamentos.chegada.desfazer')->middleware('permission:agendamentos.editar');
    Route::delete('/agendamentos/{id}/cancelar', [AgendamentoController::class, 'cancelar'])->name('agendamentos.cancelar')->middleware('permission:agendamentos.cancelar');

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
