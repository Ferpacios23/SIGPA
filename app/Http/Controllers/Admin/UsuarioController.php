<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\PrestamoAula;
use App\Models\PrestamoEquipo;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

 
class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $rolId  = $request->get('rol');
        $estado = $request->get('estado');

        $users = User::with('profile.role')
            ->when($search, fn($q) => $q->where(fn($q2) => $q2
                ->where('name',  'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($rolId, fn($q) => $q->whereHas('profile', fn($q2) => $q2->where('role_id', $rolId)))
            ->when($estado !== null && $estado !== '', fn($q) => $q->whereHas('profile', fn($q2) => $q2->where('activo', $estado)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::where('activo', true)->get();
        return view('admin.usuarios.index', compact('users', 'roles', 'search', 'rolId', 'estado'));
    }
 
    public function create()
    {
        $roles = Role::where('activo', true)->get();
        return view('admin.usuarios.create', compact('roles'));
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:users'],
            'role_id'        => ['required', 'exists:roles,id'],
            'identificacion' => ['nullable', 'string', 'max:20', 'unique:user_profiles'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'dependencia'    => ['nullable', 'string', 'max:100'],
        ]);

        $tempPassword = Str::password(10, letters: true, numbers: true, symbols: false, spaces: false);

        $user = User::create([
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'password'             => $tempPassword,
            'must_change_password' => true,
        ]);

        UserProfile::create([
            'user_id'        => $user->id,
            'role_id'        => $data['role_id'],
            'identificacion' => $data['identificacion'] ?? null,
            'telefono'       => $data['telefono'] ?? null,
            'dependencia'    => $data['dependencia'] ?? null,
            'activo'         => true,
        ]);

        return redirect()->route('admin.usuarios.index')
                         ->with('success', "Usuario {$user->name} creado correctamente.")
                         ->with('temp_password', $tempPassword)
                         ->with('temp_password_user', $user->name);
    }
 
    public function edit(User $usuario)
    {
        $usuario->load('profile.role');
        $roles = Role::where('activo', true)->get();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }
 
    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', "unique:users,email,{$usuario->id}"],
            'password'       => ['nullable', 'min:8', 'confirmed'],
            'role_id'        => ['required', 'exists:roles,id'],
            'identificacion' => ['nullable', 'string', 'max:20', "unique:user_profiles,identificacion,{$usuario->profile?->id}"],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'dependencia'    => ['nullable', 'string', 'max:100'],
            'activo'         => ['boolean'],
        ]);
 
        $usuario->update([
            'name'  => $data['name'],
            'email' => $data['email'],
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);
 
        $usuario->profile()->updateOrCreate(
            ['user_id' => $usuario->id],
            [
                'role_id'        => $data['role_id'],
                'identificacion' => $data['identificacion'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'dependencia'    => $data['dependencia'] ?? null,
                'activo'         => $data['activo'] ?? true,
            ]
        );
 
        return redirect()->route('admin.usuarios.index')
                         ->with('success', "Usuario {$usuario->name} actualizado.");
    }
 
    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $prestamosAulas   = PrestamoAula::where('user_id', $usuario->id)->count();
        $prestamosEquipos = PrestamoEquipo::where('user_id', $usuario->id)->count();
        $total            = $prestamosAulas + $prestamosEquipos;

        if ($total > 0) {
            $detalle = collect([
                $prestamosAulas   ? "{$prestamosAulas} préstamo(s) de aulas"   : null,
                $prestamosEquipos ? "{$prestamosEquipos} préstamo(s) de equipos" : null,
            ])->filter()->implode(' y ');

            return back()->with(
                'error',
                "No se puede eliminar a {$usuario->name}: tiene {$detalle} registrados. Desactiva la cuenta en su lugar."
            );
        }

        $usuario->delete();
        return redirect()->route('admin.usuarios.index')
                         ->with('success', 'Usuario eliminado.');
    }
}
 