<?php


namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
 
class EquipoController extends Controller
{
    public function index()
    {
        $fueraDeServicio  = ['dañado', 'dado_de_baja'];
        $equipos          = Equipo::latest()->paginate(20);
        $equiposDisponibles = Equipo::where('activo', true)->where('disponible', true)->count();
        $equiposPrestados   = Equipo::where('activo', true)
                                    ->where('disponible', false)
                                    ->whereNotIn('estado_fisico', $fueraDeServicio)
                                    ->count();
        $totalEquipos       = Equipo::where('activo', true)->count();

        return view('admin.equipos.index', compact('equipos', 'equiposDisponibles', 'equiposPrestados', 'totalEquipos'));
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_inventario'       => ['required', 'string', 'max:50', 'unique:equipos'],
            'nombre'                  => ['required', 'string', 'max:100'],
            'marca'                   => ['nullable', 'string', 'max:80'],
            'modelo'                  => ['nullable', 'string', 'max:80'],
            'descripcion'             => ['nullable', 'string', 'max:255'],
            'estado_fisico'           => ['required', 'in:bueno,regular,dañado,dado_de_baja'],
            'ubicacion_almacenamiento'=> ['nullable', 'string', 'max:150'],
            'fecha_adquisicion'       => ['nullable', 'date'],
        ]);
        Equipo::create($data + ['disponible' => true, 'activo' => true]);
        return redirect()->route('admin.equipos.index')->with('success', 'Equipo registrado correctamente.');
    }
 
    public function update(Request $request, Equipo $equipo)
    {
        $data = $request->validate([
            'codigo_inventario'       => ['required', 'string', 'max:50', "unique:equipos,codigo_inventario,{$equipo->id}"],
            'nombre'                  => ['required', 'string', 'max:100'],
            'marca'                   => ['nullable', 'string', 'max:80'],
            'modelo'                  => ['nullable', 'string', 'max:80'],
            'descripcion'             => ['nullable', 'string', 'max:255'],
            'estado_fisico'           => ['required', 'in:bueno,regular,dañado,dado_de_baja'],
            'ubicacion_almacenamiento'=> ['nullable', 'string', 'max:150'],
            'fecha_adquisicion'       => ['nullable', 'date'],
            'disponible'              => ['boolean'],
            'activo'                  => ['boolean'],
        ]);
        $equipo->update($data);
        return redirect()->route('admin.equipos.index')->with('success', 'Equipo actualizado.');
    }
 
    public function destroy(Equipo $equipo)
    {
        $equipo->delete();
        return redirect()->route('admin.equipos.index')->with('success', 'Equipo eliminado.');
    }

    public function checkCodigo(Request $request)
    {
        $codigo  = $request->query('codigo', '');
        $exclude = $request->query('exclude'); // id del equipo al editar

        $query = Equipo::where('codigo_inventario', $codigo);
        if ($exclude) {
            $query->where('id', '!=', $exclude);
        }

        return response()->json(['exists' => $query->exists()]);
    }
}