<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DocenteController extends Controller
{
    public function index()
    {
        return view('dashboard.docente');
    }
}
