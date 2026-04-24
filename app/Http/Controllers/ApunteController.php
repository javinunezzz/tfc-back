<?php

namespace App\Http\Controllers;

use App\Models\Apunte;
use App\Models\Descarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ApunteController extends Controller
{

    /**
     * Obtener apuntes filtrados por categoría y asignatura.
     *
     * @param string $categoria_nombre Nombre parcial o completo de la categoría.
     * @param int $asignatura_id ID de la asignatura.
     */
    public function getApuntesByFilters(string $categoria_nombre, int $asignatura_id)
    {
        try {
            $query = Apunte::query();

            if ($categoria_nombre) {
                $query->whereHas('categoria', function($q) use ($categoria_nombre) {
                    $q->where('nombre', 'LIKE', "%{$categoria_nombre}%");
                });
            }

            if ($asignatura_id) {
                $query->where('asignatura_id', $asignatura_id);
            }

            $total = $query->count();

            $apuntes = $query->with(['user:id,name,username', 'categoria:id,nombre', 'asignatura:id,nombre'])
                            ->get();

            if ($apuntes->isEmpty()) {
                return response()->json([
                    'total_apuntes' => 0,
                    'apuntes' => []
                ], 200);
            }

            $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);

            return response()->json([
                'total_apuntes' => $total,
                'apuntes' => $apuntes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los apuntes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un apunte por su ID.
     *
     * @param string $id ID del apunte a eliminar.
     */
    public function destroy(string $id)
    {
        $apunte = Apunte::find($id);

        if (!$apunte) {
            return response()->json([
                'message' => 'No se encontró el apunte.'
            ], 404);
        }

        $apunte->delete();

        return response()->json([
            'message' => 'Apunte eliminado con éxito.'
        ]);
    }

    /**
     * Crear un nuevo apunte con archivo PDF.
     *
     * @param Request $request Datos del apunte y archivo PDF.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'categoria_id' => 'required|exists:categorias,id',
                'asignatura_id' => 'required|exists:asignaturas,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'pdf' => 'required|mimes:pdf|max:20480',
            ]);

            if ($request->hasFile('pdf')) {
                $file = $request->file('pdf');
                $uuid = (string) Str::uuid();
                $slugTitulo = Str::slug($request->input('titulo'));
                $fileName = $uuid . '.' . $slugTitulo . '.' . $file->getClientOriginalExtension();
                $destino = config('app.upload_pdf', 'pdf');
                $file->storeAs($destino, $fileName);
            } else {
                return response()->json(['message' => 'No se ha proporcionado un archivo PDF válido.'], 400);
            }

            $apunte = Apunte::create([
                'user_id' => Auth::id(), // Usa el ID del usuario autenticado
                'categoria_id' => $request->input('categoria_id'),
                'asignatura_id' => $request->input('asignatura_id'),
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'pdf' => $fileName,
            ]);

            return response()->json($apunte, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el apunte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener apuntes del usuario autenticado.
     */
    public function misApuntes()
    {
        try {
            $apuntes = Apunte::where('user_id', Auth::id())
                            ->with(['categoria:id,nombre', 'asignatura:id,nombre'])
                            ->get();

            if ($apuntes->isEmpty()) {
                return response()->json([], 200);
            }

            $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);
            return response()->json($apuntes);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener tus apuntes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar el PDF de un apunte verificando permisos y límites.
     *
     * @param string $id ID del apunte a descargar.
     */
    public function download(string $id)
    {
        $apunte = Apunte::find($id);

        if (!$apunte) {
            return response()->json([
                'message' => 'Apunte no encontrado.'
            ], 404);
        }

        $user = Auth::user();

        if ($user->rol == 'FREE' ) {
            $descargasHoy = Descarga::where('user_id', Auth::id())
                ->whereDate('fecha_descarga', now())
                ->count();

            if ($descargasHoy >= 2) {
                return response()->json([
                    'message' => 'Has alcanzado el límite de descargas diarias (2).',
                ], 429);
            }
        }

        $rutaPDF = config('app.upload_pdf') . $apunte->pdf;

        if (!Storage::exists($rutaPDF)) {
            return response()->json([
                'message' => 'El archivo PDF no se encuentra en el servidor.',
            ], 404);
        }

        // Registrar la descarga
        Descarga::create([
            'user_id' => Auth::id(),
            'apunte_id' => $id,
            'fecha_descarga' => now()
        ]);

        return Storage::download($rutaPDF);
    }

    /**
     * Buscar apuntes por término dentro de categoría y asignatura.
     *
     * @param Request $request Parámetro de búsqueda 'q' opcional.
     * @param string $categoria_nombre Nombre de la categoría.
     * @param int $asignatura_id ID de la asignatura.
     */
    public function buscar(Request $request, string $categoria_nombre, int $asignatura_id)
    {
        try {
            $request->validate([
                'q' => 'nullable|string'
            ]);

            $termino = $request->input('q');

            $query = Apunte::query();

            // Filtrar por categoría de la URL
            $query->whereHas('categoria', function($q) use ($categoria_nombre) {
                $q->where('nombre', $categoria_nombre);
            });

            // Filtrar por asignatura de la URL
            $query->where('asignatura_id', $asignatura_id);

            // Buscar por término si se proporciona
            if ($termino) {
                $query->where(function($q) use ($termino) {
                    $q->where('titulo', 'LIKE', "%{$termino}%")
                      ->orWhere('descripcion', 'LIKE', "%{$termino}%");
                });
            }

            $apuntes = $query->with(['user:id,name,username', 'categoria:id,nombre', 'asignatura:id,nombre'])
                            ->get();

            if ($apuntes->isEmpty()) {
                return response()->json([], 200);
            }

            $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);
            return response()->json($apuntes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener los 5 apuntes más recientes.
     */
    public function recientes()
    {
        try {
            $apuntes = Apunte::with(['user:id,name', 'categoria:id,nombre', 'asignatura:id,nombre'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

            $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);
            return response()->json($apuntes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener apuntes recientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del usuario autenticado sobre sus apuntes.
     */
    public function misEstadisticas()
    {
        try {
            $userId = Auth::id();

            $estadisticas = [
                'total_apuntes' => Apunte::where('user_id', $userId)->count(),
                'apuntes_por_categoria' => Apunte::where('user_id', $userId)
                    ->selectRaw('categoria_id, count(*) as total')
                    ->groupBy('categoria_id')
                    ->with('categoria:id,nombre')
                    ->get(),
                'apuntes_por_asignatura' => Apunte::where('user_id', $userId)
                    ->selectRaw('asignatura_id, count(*) as total')
                    ->groupBy('asignatura_id')
                    ->with('asignatura:id,nombre')
                    ->get(),
                'ultimo_apunte' => Apunte::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->first()
            ];

            return response()->json($estadisticas);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar la descripción de un apunte propio.
     *
     * @param Request $request Nueva descripción.
     * @param string $id ID del apunte a modificar.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'descripcion' => 'required|string'
            ]);

            $apunte = Apunte::find($id);

            if (!$apunte) {
                return response()->json([
                    'message' => 'Apunte no encontrado.'
                ], 404);
            }

            // Verificar que el usuario es el propietario del apunte
            if ($apunte->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'No tienes permiso para modificar este apunte.'
                ], 403);
            }

            $apunte->descripcion = $request->descripcion;
            $apunte->save();

            return response()->json([
                'message' => 'Descripción actualizada con éxito.',
                'apunte' => $apunte
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la descripción: ' . $e->getMessage()
            ], 500);
        }
    }
}
