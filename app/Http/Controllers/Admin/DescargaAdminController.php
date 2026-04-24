<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Descarga;
use Illuminate\Http\Request;

class DescargaAdminController extends Controller
{
    /**
     * Obtiene la lista de todas las descargas con usuario y apunte relacionados.
     *
     * @param Request $request La solicitud HTTP entrante.
     */
    public function index(Request $request)
    {
        $descargas = Descarga::with(['user:id,name', 'apunte:id,titulo'])->get();

        if ($descargas->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron descargas.'
            ], 404);
        }

        // Oculta los IDs redundantes.
        $descargas->makeHidden(['user_id', 'apunte_id']);

        return response()->json($descargas);
    }

    /**
     * Crea una nueva descarga con los datos proporcionados.
     *
     * @param Request $request La solicitud HTTP con los datos de descarga.
     */
    public function store(Request $request)
    {
        try {
            $descargas = Descarga::create([
                'user_id' => $request->input('user_id'),
                'apunte_id' => $request->input('apunte_id'),
                'fecha_descarga' => $request->input('fecha_descarga'),
            ]);

            return response()->json(
                $descargas
                , 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la descarga. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una descarga específica junto con el usuario y apunte relacionados.
     *
     * @param string $id El identificador de la descarga.
     */
    public function show(string $id)
    {
        $descarga = Descarga::with(['user:id,name', 'apunte:id,titulo'])->find($id);

        if (!$descarga) {
            return response()->json([
                'message' => 'Descarga no encontrada.'
            ], 404);
        }

        // Oculta los IDs redundantes.
        $descarga->makeHidden(['user_id', 'apunte_id']);

        return response()->json($descarga);
    }

    /**
     * Actualiza los datos de una descarga existente.
     *
     * @param Request $request La solicitud HTTP con los datos para actualizar.
     * @param string $id El identificador de la descarga a actualizar.
     */
    public function update(Request $request, string $id)
    {
        $descarga = Descarga::find($id);

        if (!$descarga) {
            return response()->json([
                'message' => 'No se encontró la descarga.'
            ], 404);
        }

        try {
            $descarga->update([
                'user_id' => $request->input('user_id', $descarga->user_id),
                'apunte_id' => $request->input('apunte_id', $descarga->apunte_id),
                'fecha_descarga' => $request->input('fecha_descarga', $descarga->fecha_descarga),
            ]);

            return response()->json(
                $descarga
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar la descarga. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una descarga específica.
     *
     * @param string $id El identificador de la descarga a eliminar.
     */
    public function destroy(string $id)
    {
        $descarga = Descarga::find($id);

        if (!$descarga) {
            return response()->json([
                'message' => 'No se encontró la descarga.'
            ], 404);
        }

        $descarga->delete();

        return response()->json([
            'message' => 'Descarga eliminado con éxito.'
        ]);
    }
}
