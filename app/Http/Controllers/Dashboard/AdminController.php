<?php
// app/Http/Controllers/Dashboard/AdminController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Equipo;
use App\Models\PrestamoAula;
use App\Models\Role;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        // ── Usuarios ─────────────────────────────────────────────
        $totalUsers  = User::count();
        $recentUsers = User::with('profile.role')->latest()->take(5)->get();

        // ── Aulas ─────────────────────────────────────────────────
        $aulas      = Aula::orderBy('codigo')->get();
        $totalAulas = $aulas->count();
        $aulasLibres = $aulas->where('estado', 'disponible')->where('activa', true)->count();

        // ── Equipos ───────────────────────────────────────────────
        $fueraDeServicio = ['dañado', 'dado_de_baja'];
        $totalEquipos    = Equipo::where('activo', true)->count();
        $equiposLibres   = Equipo::where('activo', true)->where('disponible', true)->count();
        $equiposPrestados= Equipo::where('activo', true)
                               ->where('disponible', false)
                               ->whereNotIn('estado_fisico', $fueraDeServicio)
                               ->count();
        $recentEquipos   = Equipo::where('activo', true)->latest()->take(6)->get();

        // ── Préstamos ─────────────────────────────────────────────
        $todayLoans   = PrestamoAula::whereDate('fecha_prestamo', today())->count();
        $pendingLoans = PrestamoAula::where('estado', 'pendiente')->count();
        $recentLoans  = PrestamoAula::with(['user', 'aula'])
                            ->latest()
                            ->take(8)
                            ->get();

        // ── Roles ─────────────────────────────────────────────────
        $roles = Role::withCount('profiles')->where('activo', true)->get();

        return view('dashboard.admin', compact(
            'totalUsers',
            'recentUsers',
            'aulas',
            'totalAulas',
            'aulasLibres',
            'totalEquipos',
            'equiposLibres',
            'equiposPrestados',
            'recentEquipos',
            'todayLoans',
            'pendingLoans',
            'recentLoans',
            'roles',
        ));
    }
}