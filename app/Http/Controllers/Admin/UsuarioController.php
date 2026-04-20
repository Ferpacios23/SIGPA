<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

 
class UsuarioController extends Controller
{
    public function index()
    {
        $users = User::with('profile.role')->latest()->paginate(15);
        $roles = Role::where('activo', true)->get();
        return view('admin.usuarios.index', compact('users', 'roles'));
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
            'password'       => ['required', 'min:8', 'confirmed'],
            'role_id'        => ['required', 'exists:roles,id'],
            'identificacion' => ['nullable', 'string', 'max:20', 'unique:user_profiles'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'dependencia'    => ['nullable', 'string', 'max:100'],
        ]);
 
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
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
                         ->with('success', "Usuario {$user->name} creado correctamente.");
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
        // Nunca borrar el propio admin
        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')
                         ->with('success', 'Usuario eliminado.');
    }
}
 