<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apunte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApunteAdminController extends Controller
{
    /**
     * Obtiene la lista de apuntes con información adicional de usuario, categoría y asignatura.
     *
     * @param Request $request La solicitud HTTP entrante.
     */
    public function index2(Request $request)
    {
        $apuntes = Apunte::with(['user:id,name,username', 'categoria:id,nombre', 'asignatura:id,nombre'])->get();

        if ($apuntes->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron apuntes.'
            ], 404);
        }

        // Oculta los IDs redundantes
        $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);

        return response()->json($apuntes);
    }

    /**
     * Almacena un nuevo apunte en la base de datos.
     *
     * @param Request $request La solicitud HTTP con los datos del apunte.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'categoria_id' => 'required|exists:categorias,id',
                'asignatura_id' => 'required|exists:asignaturas,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'pdf' => 'required|mimes:pdf|max:20480',
            ]);

            if ($request->hasFile('pdf')) {
                $file = $request->file('pdf');
                $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $fileName = $uuid . '.' . $request->input('titulo') . '.' . $file->getClientOriginalExtension();
                $destino = env('UPLOAD_PDF', 'pdf');
                $file->storeAs($destino, $fileName);
            } else {
                return response()->json(['message' => 'No se ha proporcionado un archivo PDF válido.'], 400);
            }

            $apunte = Apunte::create([
                'user_id' => $request->input('user_id'),
                'categoria_id' => $request->input('categoria_id'),
                'asignatura_id' => $request->input('asignatura_id'),
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'pdf' => $fileName,
            ]);

            return response()->json($apunte, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear el apunte. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de un apunte específico.
     *
     * @param string $id Identificador único del apunte.
     */
    public function show2(string $id)
    {
        $apunte = Apunte::with(['user:id,name', 'categoria:id,nombre', 'asignatura:id,nombre'])->find($id);

        if (!$apunte) {
            return response()->json([
                'message' => 'Apunte no encontrado.'
            ], 404);
        }

        $apunte->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);

        return response()->json($apunte);
    }

    /**
     * Actualiza los datos de un apunte existente.
     *
     * @param Request $request La solicitud HTTP con los nuevos datos del apunte.
     * @param string $id Identificador único del apunte a actualizar.
     */
    public function update(Request $request, string $id)
    {
        $apunte = Apunte::find($id);

        if (!$apunte) {
            return response()->json([
                'message' => 'No se encontró el apunte.'
            ], 404);
        }

        try {
            $apunte->update([
                'user_id' => $request->input('user_id', $apunte->user_id),
                'categoria_id' => $request->input('categoria_id', $apunte->categoria_id),
                'asignatura_id' => $request->input('asignatura_id', $apunte->asignatura_id),
                'descripcion' => $request->input('descripcion', $apunte->descripcion),
            ]);

            return response()->json($apunte);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar el apunte. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un apunte de la base de datos.
     *
     * @param string $id Identificador único del apunte a eliminar.
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
     * Descarga el archivo PDF de un apunte.
     *
     * @param string $id Identificador único del apunte.
     */
    public function download(string $id)
    {
        $apunte = Apunte::find($id);

        if (!$apunte || !Storage::exists($apunte->pdf)) {
            return response()->json([
                'message' => 'Archivo no encontrado.'
            ], 404);
        }

        return Storage::download($apunte->pdf);
    }

    /**
     * Busca apuntes filtrando por categoría y asignatura opcionalmente.
     *
     * @param Request $request La solicitud HTTP con filtros.
     */
    public function buscar(Request $request)
    {
        try {
            $query = Apunte::with(['user:id,name,username', 'categoria:id,nombre', 'asignatura:id,nombre']);

            if ($request->has('categoria_id') && $request->categoria_id != '') {
                $query->where('categoria_id', $request->categoria_id);

                if ($request->has('asignatura_id') && $request->asignatura_id != '') {
                    $query->where('asignatura_id', $request->asignatura_id);
                }
            }

            $apuntes = $query->get();

            if ($apuntes->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron apuntes con los filtros seleccionados.'
                ], 404);
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
     * Busca apuntes por nombre de usuario (username).
     *
     * @param Request $request La solicitud HTTP con el parámetro username.
     */
    public function buscarPorUsername(Request $request)
    {
        try {
            $username = $request->input('username');

            $apuntes = Apunte::with(['user:id,name,username', 'categoria:id,nombre', 'asignatura:id,nombre'])
                ->whereHas('user', function($query) use ($username) {
                    $query->where('username', 'LIKE', "%{$username}%");
                })
                ->get();

            if ($apuntes->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron apuntes para este usuario.'
                ], 404);
            }

            $apuntes->makeHidden(['user_id', 'categoria_id', 'asignatura_id']);

            return response()->json($apuntes);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
}
