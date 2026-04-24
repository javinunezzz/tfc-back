<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Suscripcion;
use Illuminate\Http\Request;

class SuscripcionAdminController extends Controller
{
    /**
     * Obtiene todas las suscripciones con los datos del usuario relacionados.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $suscripciones = Suscripcion::with(['user:id,name'])->get();

        if ($suscripciones->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron suscripciones.'
            ], 404);
        }

        // Oculta los IDs redundantes.
        $suscripciones->makeHidden(['user_id']);

        return response()->json($suscripciones);
    }

    /**
     * Crea una nueva suscripción.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $suscripcion = Suscripcion::create([
                'user_id' => $request->input('user_id'),
                'fecha_inicio' => $request->input('fecha_inicio'),
                'fecha_fin' => $request->input('fecha_fin'),
                'estado' => $request->input('estado'),
            ]);

            return response()->json($suscripcion, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la suscripción. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una suscripción específica con usuario relacionado.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $suscripcion = Suscripcion::with(['user:id,name'])->find($id);

        if (!$suscripcion) {
            return response()->json([
                'message' => 'Suscripción no encontrada.'
            ], 404);
        }

        // Oculta los IDs redundantes.
        $suscripcion->makeHidden(['user_id']);

        return response()->json($suscripcion);
    }

    /**
     * Actualiza una suscripción existente.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $suscripcion = Suscripcion::find($id);

        if (!$suscripcion) {
            return response()->json([
                'message' => 'No se encontró la suscripción.'
            ], 404);
        }

        try {
            $suscripcion->update([
                'user_id' => $request->input('user_id', $suscripcion->user_id),
                'fecha_inicio' => $request->input('fecha_inicio', $suscripcion->fecha_inicio),
                'fecha_fin' => $request->input('fecha_fin', $suscripcion->fecha_fin),
                'estado' => $request->input('estado', $suscripcion->estado),
            ]);

            return response()->json($suscripcion);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar la suscripción. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una suscripción específica.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $suscripcion = Suscripcion::find($id);

        if (!$suscripcion) {
            return response()->json([
                'message' => 'No se encontró la suscripción.'
            ], 404);
        }

        $suscripcion->delete();

        return response()->json([
            'message' => 'Suscripción eliminada con éxito.'
        ]);
    }
}
