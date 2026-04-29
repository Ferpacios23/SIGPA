<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DocenteController extends Controller
{
    private function docenteRole(): \App\Models\Role
    {
        return Role::where('slug', 'docente')->firstOrFail();
    }

    // ── Listado de docentes ──────────────────────────────────────
    public function index(Request $request)
    {
        $role = $this->docenteRole();

        $query = User::whereHas('profile', fn($q) => $q->where('role_id', $role->id))
                     ->with('profile');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhereHas('profile', fn($p) =>
                      $p->where('identificacion', 'like', '%'.$request->search.'%')
                        ->orWhere('dependencia', 'like', '%'.$request->search.'%')
                  );
            });
        }

        if ($request->filled('activo')) {
            $query->whereHas('profile', fn($q) =>
                $q->where('activo', $request->activo)
            );
        }

        $docentes = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.docentes.index', compact('docentes'));
    }

    // ── Registrar docente (sin acceso al sistema) ────────────────
    public function store(Request $request)
    {
        $role = $this->docenteRole();

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['nullable', 'string', 'min:8'],
            'identificacion' => ['nullable', 'string', 'max:20', 'unique:user_profiles,identificacion'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'dependencia'    => ['nullable', 'string', 'max:100'],
        ]);

        $passwordInicial = $data['password'] ?? 'docente123';

        DB::transaction(function () use ($data, $role, $passwordInicial) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($passwordInicial),
            ]);

            UserProfile::create([
                'user_id'        => $user->id,
                'role_id'        => $role->id,
                'identificacion' => $data['identificacion'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'dependencia'    => $data['dependencia'] ?? null,
                'activo'         => true,
            ]);
        });

        return redirect()->route('admin.docentes.index')
                         ->with('success', "Docente {$data['name']} registrado. Contraseña inicial: {$passwordInicial}");
    }

    // ── Actualizar datos del docente ─────────────────────────────
    public function update(Request $request, User $docente)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', "unique:users,email,{$docente->id}"],
            'identificacion' => ['nullable', 'string', 'max:20',
                                  "unique:user_profiles,identificacion,{$docente->profile?->id}"],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'dependencia'    => ['nullable', 'string', 'max:100'],
            'activo'         => ['boolean'],
        ]);

        DB::transaction(function () use ($data, $docente) {
            $docente->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            $docente->profile()->update([
                'identificacion' => $data['identificacion'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'dependencia'    => $data['dependencia'] ?? null,
                'activo'         => $data['activo'] ?? true,
            ]);
        });

        return redirect()->route('admin.docentes.index')
                         ->with('success', "Docente {$docente->name} actualizado.");
    }

    // ── Eliminar / desactivar docente ────────────────────────────
    public function destroy(User $docente)
    {
        // Verificar que tiene préstamos activos
        $tieneActivos = $docente->prestamosAulas()
                                ->whereIn('estado', ['aprobado', 'activo'])
                                ->exists();

        if ($tieneActivos) {
            return back()->with('error', 'No se puede eliminar: el docente tiene préstamos activos.');
        }

        $docente->delete();

        return redirect()->route('admin.docentes.index')
                         ->with('success', 'Docente eliminado del sistema.');
    }
}
