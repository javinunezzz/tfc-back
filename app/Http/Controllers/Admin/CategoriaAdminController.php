<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaAdminController extends Controller
{
    /**
     * Obtiene la lista de todas las categorías.
     *
     * @param Request $request La solicitud HTTP entrante.
     */
    public function index(Request $request)
    {
        $categorias = Categoria::all();

        if ($categorias->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron categorias.'
            ], 404);
        }

        return response()->json($categorias);
    }

    /**
     * Almacena una nueva categoría.
     *
     * @param Request $request La solicitud HTTP con los datos de la categoría.
     */
    public function store(Request $request)
    {
        try {
            $categorias = Categoria::create([
                'nombre' => $request->input('nombre'),
            ]);

            return response()->json(
                $categorias
                , 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la categoria. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una categoría específica.
     *
     * @param string $id El identificador de la categoría.
     */
    public function show(string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoria no encontrado.'
            ], 404);
        }

        return response()->json($categoria);
    }

    /**
     * Actualiza los datos de una categoría existente.
     *
     * @param Request $request La solicitud HTTP con los nuevos datos de la categoría.
     * @param string $id El identificador de la categoría a actualizar.
     */
    public function update(Request $request, string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'No se encontró el edificio.'
            ], 404);
        }

        try {
            $categoria->update([
                'nombre' => $request->input('nombre', $categoria->nombre),
            ]);

            return response()->json(
                $categoria
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar la categoria. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una categoría de la base de datos.
     *
     * @param string $id El identificador de la categoría a eliminar.
     */
    public function destroy(string $id)
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'No se encontró la categoria.'
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'message' => 'Categoria eliminado con éxito.'
        ]);
    }
}
