<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function index()
    {
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'El módulo de docentes aún no está habilitado.');
    }
}
