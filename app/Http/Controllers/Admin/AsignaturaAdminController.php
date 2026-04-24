<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asignatura;
use Illuminate\Http\Request;

class AsignaturaAdminController extends Controller
{
    /**
     * Obtiene la lista de todas las asignaturas.
     *
     * @param Request $request La solicitud HTTP entrante.
     */
    public function index(Request $request)
    {
        $asignaturas = Asignatura::all();

        if ($asignaturas->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron asignaturas.'
            ], 404);
        }

        return response()->json($asignaturas);
    }

    /**
     * Almacena una nueva asignatura.
     *
     * @param Request $request La solicitud HTTP con los datos de la asignatura.
     */
    public function store(Request $request)
    {
        try {
            $asignatura = Asignatura::create([
                'nombre' => $request->input('nombre'),
            ]);

            return response()->json(
                $asignatura
                , 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la Asignatura. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una asignatura específica.
     *
     * @param string $id El identificador de la asignatura.
     */
    public function show(string $id)
    {
        $asignatura = Asignatura::find($id);

        if (!$asignatura) {
            return response()->json([
                'message' => 'Asignatura no encontrado.'
            ], 404);
        }

        return response()->json($asignatura);
    }

    /**
     * Actualiza los datos de una asignatura existente.
     *
     * @param Request $request La solicitud HTTP con los nuevos datos de la asignatura.
     * @param string $id El identificador de la asignatura a actualizar.
     */
    public function update(Request $request, string $id)
    {
        $asignatura = Asignatura::find($id);

        if (!$asignatura) {
            return response()->json([
                'message' => 'No se encontró la Asignatura.'
            ], 404);
        }

        try {
            $asignatura->update([
                'nombre' => $request->input('nombre', $asignatura->nombre),
            ]);

            return response()->json(
                $asignatura
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar la Asignatura. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una asignatura de la base de datos.
     *
     * @param string $id El identificador de la asignatura a eliminar.
     */
    public function destroy(string $id)
    {
        $asignatura = Asignatura::find($id);

        if (!$asignatura) {
            return response()->json([
                'message' => 'No se encontró la Asignatura.'
            ], 404);
        }

        $asignatura->delete();

        return response()->json([
            'message' => 'Asignatura eliminado con éxito.'
        ]);
    }
}
