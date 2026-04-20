<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Role;
 
class RolController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('profiles')->orderBy('id')->get();
        return view('admin.roles.index', compact('roles'));
    }
 
    public function show(Role $rol)
    {
        $rol->load(['profiles.user']);
        return view('admin.roles.show', compact('rol'));
    }
}