<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $usuarios = User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellidos', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->with('role')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.usuarios', [
            'usuarios' => $usuarios,
            'roles' => Role::where('es_activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['es_activo'] = $data['es_activo'] ?? true;
        // 'hashed' cast se encarga del hash

        $usuario = User::create($data);

        return redirect()
            ->route('dashboard.usuarios')
            ->with('success', "Usuario {$usuario->nombre_completo} creado correctamente.");
    }

    public function update(UpdateUsuarioRequest $request, User $usuario): RedirectResponse
    {
        $data = $request->validated();

        if (($data['es_activo'] ?? true) == false && $usuario->id === auth()->id()) {
            return redirect()
                ->route('dashboard.usuarios')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        // Contraseña vacía → se mantiene la actual
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['es_activo'] = $data['es_activo'] ?? true;

        $usuario->update($data);

        return redirect()
            ->route('dashboard.usuarios')
            ->with('success', "Usuario {$usuario->nombre_completo} actualizado correctamente.");
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()
                ->route('dashboard.usuarios')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $usuario->nombre_completo;
        $usuario->delete();

        return redirect()
            ->route('dashboard.usuarios')
            ->with('success', "Usuario {$nombre} eliminado.");
    }
}
