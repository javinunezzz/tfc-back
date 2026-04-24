<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Obtener todas las categorías.
     */
    public function index()
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
     * Obtener una categoría por su ID.
     *
     * @param string $id ID de la categoría a buscar.
     */
    public function showById(string $id)
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
     * Obtener una categoría por su nombre.
     *
     * @param string $nombre Nombre de la categoría a buscar.
     */
    public function showByNombre($nombre)
    {
        try {
            $categoria = Categoria::where('nombre', $nombre)->first();

            if (!$categoria) {
                return response()->json([
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al buscar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }
}
