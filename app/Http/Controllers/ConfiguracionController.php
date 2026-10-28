<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualizarPerfilRequest;
use App\Http\Requests\GuardarUmbralRequest;
use App\Models\ConfiguracionAlerta;
use App\Models\TipoEnfermedad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    private const ORDEN_NIVEL = ['Bajo' => 0, 'Medio' => 1, 'Alto' => 2];

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'enfermedad_id' => ['nullable', 'integer', 'exists:tipos_enfermedades,id'],
        ]);

        $umbrales = ConfiguracionAlerta::with('tipoEnfermedad')
            ->when($filtros['enfermedad_id'] ?? null, fn ($query, $id) => $query->where('tipo_enfermedad_id', $id))
            ->get()
            ->sortBy([
                fn ($umbral) => $umbral->tipoEnfermedad?->nombre ?? '',
                fn ($umbral) => $umbral->nombre_parametro,
                fn ($umbral) => self::ORDEN_NIVEL[$umbral->nivel_alerta] ?? 0,
            ])
            ->values();

        return view('dashboard.configuracion', [
            'usuario' => $request->user(),
            'tiposEnfermedad' => TipoEnfermedad::where('es_activo', true)->orderBy('nombre')->get(),
            'umbrales' => $umbrales,
            'etiquetasParametros' => $this->etiquetasParametros(),
            'filtros' => $filtros,
        ]);
    }

    public function actualizarPerfil(ActualizarPerfilRequest $request): RedirectResponse
    {
        $usuario = $request->user();

        $usuario->fill($request->safe()->only([
            'nombre',
            'apellidos',
            'email',
            'telefono',
            'especialidad',
            'registro_medico',
        ]));

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->input('password'));
        }

        $usuario->save();

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    public function cerrarSesiones(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_actual' => ['required', 'current_password'],
        ]);

        Auth::logoutOtherDevices($data['password_actual']);

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', 'Se cerró la sesión en todos los demás dispositivos.');
    }

    public function guardarUmbral(GuardarUmbralRequest $request): RedirectResponse
    {
        ConfiguracionAlerta::create($request->datosUmbral());

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', 'Umbral de alerta creado. Los próximos controles lo tendrán en cuenta.');
    }

    public function actualizarUmbral(GuardarUmbralRequest $request, ConfiguracionAlerta $configuracionAlerta): RedirectResponse
    {
        $configuracionAlerta->update($request->datosUmbral());

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', 'Umbral de alerta actualizado.');
    }

    public function cambiarEstadoUmbral(ConfiguracionAlerta $configuracionAlerta): RedirectResponse
    {
        $configuracionAlerta->update(['es_activo' => ! $configuracionAlerta->es_activo]);

        $estado = $configuracionAlerta->es_activo ? 'activado' : 'desactivado';

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', "Umbral {$estado}. Las alertas ya generadas no se modifican.");
    }

    public function eliminarUmbral(ConfiguracionAlerta $configuracionAlerta): RedirectResponse
    {
        $configuracionAlerta->delete();

        return redirect()
            ->route('dashboard.configuracion')
            ->with('success', 'Umbral eliminado. Las alertas ya generadas conservan sus valores de referencia.');
    }

    /**
     * Etiquetas legibles de los parámetros evaluables por GeneradorDeAlertas.
     *
     * @return array<string, string>
     */
    private function etiquetasParametros(): array
    {
        return [
            'presion_sistolica' => 'Presión sistólica',
            'presion_diastolica' => 'Presión diastólica',
            'frecuencia_cardiaca' => 'Frecuencia cardíaca',
            'frecuencia_respiratoria' => 'Frecuencia respiratoria',
            'temperatura' => 'Temperatura',
            'peso' => 'Peso',
            'talla' => 'Talla',
            'imc' => 'IMC',
            'glucosa' => 'Glucosa',
            'saturacion_oxigeno' => 'Saturación de oxígeno',
        ];
    }
}
