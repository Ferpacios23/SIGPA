<?php
// routes/web.php — VERSIÓN DEFINITIVA SIGPA
// Reemplaza todo el contenido de tu routes/web.php con este archivo

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\SecretariaController;
use App\Http\Controllers\Dashboard\TecnicoController;
use App\Http\Controllers\Dashboard\DocenteController;
use App\Http\Controllers\Dashboard\HorarioDocenteController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\AulaController;
use App\Http\Controllers\Admin\EquipoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\PrestamoAdminController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\DocenteController as AdminDocenteController;
use App\Http\Controllers\Secretaria\PrestamoEquipoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Raíz ──────────────────────────────────────────────────────────
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    $slug = Auth::user()->profile?->role?->slug;
    $routes = [
        'admin'      => 'dashboard.admin',
        'secretaria' => 'secretaria.dashboard',
        'tecnico'    => 'tecnico.dashboard',
    ];
    return isset($routes[$slug])
        ? redirect()->route($routes[$slug])
        : redirect()->route('login')->with('error', 'Tu rol no tiene un módulo habilitado en esta plataforma.');
});

// ══════════════════════════════════════════════════════════════════
// AUTENTICACIÓN (solo para invitados)
// ══════════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/forgot-password',        [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',       [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',        [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// Cambio de contraseña obligatorio (cualquier rol autenticado)
Route::middleware('auth')->group(function () {
    Route::get('/change-password',  [ChangePasswordController::class, 'show'])
         ->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])
         ->name('password.change.update');
});

// ══════════════════════════════════════════════════════════════════
// ADMINISTRADOR
// Todas las rutas son:  dashboard.admin, admin.usuarios.*, etc.
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])
     ->group(function () {

    // Dashboard principal del admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
         ->name('dashboard.admin');

    // Sub-módulos del admin con prefijo y nombre admin.*
    Route::prefix('admin')->name('admin.')->group(function () {

        Route::resource('usuarios', UsuarioController::class);
        Route::resource('aulas', AulaController::class);
        Route::get('aulas/{aula}/horarios',                    [AulaController::class, 'horarios'])->name('aulas.horarios');
        Route::post('aulas/{aula}/horarios',                   [AulaController::class, 'storeHorario'])->name('aulas.horarios.store');
        Route::delete('aulas/{aula}/horarios/{horario}',       [AulaController::class, 'destroyHorario'])->name('aulas.horarios.destroy');
        Route::patch('aulas/{aula}/horarios/{horario}/toggle', [AulaController::class, 'toggleHorario'])->name('aulas.horarios.toggle');
        Route::get('equipos/check-codigo', [EquipoController::class, 'checkCodigo'])->name('equipos.check-codigo');
        Route::resource('equipos',  EquipoController::class);

        Route::get('roles',       [RolController::class, 'index'])->name('roles.index');
        Route::get('roles/{rol}', [RolController::class, 'show'])->name('roles.show');

        Route::patch('prestamos/{prestamo}/aprobar',
                     [PrestamoAdminController::class, 'aprobar'])->name('prestamos.aprobar');
        Route::patch('prestamos/{prestamo}/cancelar',
                     [PrestamoAdminController::class, 'cancelar'])->name('prestamos.cancelar');

        Route::resource('horarios', HorarioController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('reportes',        [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('reportes/export', [ReporteController::class, 'export'])->name('reportes.export');

        // Docentes (entidades sin acceso al sistema)
        Route::resource('docentes', AdminDocenteController::class)->only(['index','store','update','destroy']);

        Route::get('configuracion', fn() => view('admin.configuracion'))->name('configuracion');
    });
});

// ══════════════════════════════════════════════════════════════════
// SECRETARÍA
// Ruta de entrada:  secretaria.dashboard
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:secretaria'])
     ->prefix('secretaria')
     ->name('secretaria.')
     ->group(function () {

    Route::get('/dashboard',  [SecretariaController::class, 'index'])->name('dashboard');
    Route::get('/prestamos',  [SecretariaController::class, 'prestamos'])->name('prestamos');
    Route::post('/prestamos', [SecretariaController::class, 'storePrestamo'])->name('prestamos.store');

    Route::patch('/prestamos/{prestamo}/aprobar',
                 [SecretariaController::class, 'aprobar'])->name('prestamos.aprobar');
    Route::patch('/prestamos/{prestamo}/cancelar',
                 [SecretariaController::class, 'cancelar'])->name('prestamos.cancelar');
    Route::patch('/prestamos/{prestamo}/finalizar',
                 [SecretariaController::class, 'finalizar'])->name('prestamos.finalizar');
    Route::patch('/prestamos/{prestamo}/checkin',
                 [SecretariaController::class, 'checkin'])->name('prestamos.checkin');

    Route::get('/aulas',     [SecretariaController::class, 'aulas'])->name('aulas');
    Route::patch('/horarios/{horario}/checkin',
                 [SecretariaController::class, 'checkinHorario'])->name('horarios.checkin');
    Route::get('/historial', [SecretariaController::class, 'historial'])->name('historial');

    // Préstamos de equipos (HU-09 / HU-10)
    Route::get('/equipos',                              [PrestamoEquipoController::class, 'index'])->name('equipos.index');
    Route::post('/equipos',                             [PrestamoEquipoController::class, 'store'])->name('equipos.store');
    Route::patch('/equipos/{prestamoEquipo}/aprobar',   [PrestamoEquipoController::class, 'aprobar'])->name('equipos.aprobar');
    Route::patch('/equipos/{prestamoEquipo}/entregar',  [PrestamoEquipoController::class, 'entregar'])->name('equipos.entregar');
    Route::patch('/equipos/{prestamoEquipo}/devolver',  [PrestamoEquipoController::class, 'devolver'])->name('equipos.devolver');
    Route::patch('/equipos/{prestamoEquipo}/cancelar',  [PrestamoEquipoController::class, 'cancelar'])->name('equipos.cancelar');
    // Asignar equipo directamente desde un préstamo de aula (HU-09)
    Route::post('/prestamos/{prestamo}/equipos',        [PrestamoEquipoController::class, 'asignarALoan'])->name('prestamos.equipos.asignar');
});

// ══════════════════════════════════════════════════════════════════
// TÉCNICO TI
// Ruta de entrada:  tecnico.dashboard
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:tecnico'])
     ->prefix('tecnico')
     ->name('tecnico.')
     ->group(function () {

    Route::get('/dashboard',  [TecnicoController::class, 'index'])->name('dashboard');

    // Asignaciones: ver préstamos de aula del día y asignar/devolver equipos
    Route::get('/asignaciones',                              [TecnicoController::class, 'asignaciones'])->name('asignaciones');
    Route::post('/prestamos/{prestamo}/asignar',             [TecnicoController::class, 'asignar'])->name('prestamos.asignar');
    Route::patch('/asignaciones/{asignacion}/devolver',      [TecnicoController::class, 'devolver'])->name('asignaciones.devolver');

    // Inventario + gestión de equipos por TI
    Route::get('/inventario',                          [TecnicoController::class, 'inventario'])->name('inventario');
    Route::get('/inventario/check-codigo',             [TecnicoController::class, 'checkCodigo'])->name('inventario.checkCodigo');
    Route::post('/inventario',                         [TecnicoController::class, 'storeEquipo'])->name('inventario.store');
    Route::patch('/inventario/{equipo}/estado',        [TecnicoController::class, 'cambiarEstado'])->name('inventario.estado');
});

// ══════════════════════════════════════════════════════════════════
// DOCENTE
// ══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:docente'])
     ->prefix('docente')
     ->name('docente.')
     ->group(function () {

    Route::get('/dashboard',                                    [DocenteController::class, 'index'])->name('dashboard');
    Route::get('/horario',                                      [HorarioDocenteController::class, 'index'])->name('horario');
    Route::get('/solicitudes',                                  [DocenteController::class, 'solicitudes'])->name('solicitudes.index');
    Route::post('/solicitudes',                                 [DocenteController::class, 'store'])->name('solicitudes.store');
    Route::patch('/solicitudes/{solicitud}/cancelar',           [DocenteController::class, 'cancelar'])->name('solicitudes.cancelar');
});