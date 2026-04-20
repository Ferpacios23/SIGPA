<?php

namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\PrestamoAula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class PrestamoAdminController extends Controller
{
    public function aprobar(PrestamoAula $prestamo)
    {
        if ($prestamo->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar préstamos pendientes.');
        }
        $prestamo->update([
            'estado'       => 'aprobado',
            'aprobado_por' => Auth::id(),
        ]);
        return back()->with('success', 'Préstamo aprobado correctamente.');
    }
 
    public function cancelar(Request $request, PrestamoAula $prestamo)
    {
        if (in_array($prestamo->estado, ['finalizado', 'cancelado'])) {
            return back()->with('error', 'Este préstamo ya está cerrado.');
        }
        $prestamo->update([
            'estado'             => 'cancelado',
            'cancelado_en'       => now(),
            'motivo_cancelacion' => $request->motivo ?? 'Cancelado por administrador',
        ]);
        // Liberar el aula si estaba activo
        if ($prestamo->aula) {
            $prestamo->aula->update(['estado' => 'disponible']);
        }
        return back()->with('success', 'Préstamo cancelado.');
    }
}
 