<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Exception;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{

    /**
     * Recoge todos los parametros de configuracion almacenados en la BD.
     *
     * El metodo recupera todas las configuraciones desde el modelo 'Configuracion'.
     * Si no se encuentran configuraciones, devolvera una respuesta hacia un JSON, con un mensaje de error.
     * Y si hay alguna configuracion disponible, la pasara en formato JSON.
     *
     * */
    function getParametrosConfiguracion()
    {
        $configuracion = Configuracion::all();

        if ($configuracion->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron configuraciones.'
            ], 404);
        }

        return response()->json(
            $configuracion
        );
    }

    /**
     * Obtiene el valor de un parámetro de configuración específico.
     *
     * @param string $parametro La clave del parámetro a buscar.
     */
    function getValorPorParametro(string $parametro)
    {
        $configuracion = Configuracion::where('parametro', $parametro)->first();

        if (!$configuracion) {
            return response()->json(null, 404);
        }

        return response()->json(['valor' => $configuracion->valor]);
    }

    /**
     * Actualiza un parametro de configuracion en la BD.
     *
     * Este metodo recibe una clave y un valor, desde la solicitud HTTP y actualiza
     * el parámetro correspondiente en la base de datos. Si la actualización es exitosa,
     * devuelve el objeto actualizado con un código HTTP 201.
     * Si ocurre un error, devuelve un mensaje de error con un código HTTP 500.
     *
     * @param Request $request La solicitud HTTP que contiene la clave y el valor a actualizar.
     */
    function setParametrosConfiguracion(Request $request, string $parametro)
    {
        $configuracion = Configuracion::where('parametro', $parametro)->first();

        if (!$configuracion) {
            return response()->json([
                'message' => 'Parametro no encontrado.'
            ], 404);
        }

        try {
            $configuracion->update([
                'valor' => $request->input('valor', $configuracion->valor),
            ]);

            return response()->json([
                $configuracion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar el parametro. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra un parametro de configuracion con un ID especifico, proporcionado en la solicitud.
     *
     * Este metodo busca un parametro de configuracion espefico utilizando su ID
     * Si el parámetro no se encuentra, devuelve un mensaje de error con un código HTTP 404.
     * Si se encuentra, devuelve los detalles del parámetro en formato JSON.
     *
     * @param string $id El identificador único del parámetro de configuración.
     */
    public function show(string $id)
    {
        $configuracion = Configuracion::find($id);

        if (!$configuracion) {
            return response()->json([
                'message' => 'Parametro no encontrado.'
            ], 404);
        }

        return response()->json(
            $configuracion
        );
    }

    /**
     * Elimina un parámetro de configuración específico basado en su ID.
     *
     * Este metodo busca un parámetro de configuración en la base de datos utilizando su ID.
     * Si el parámetro no se encuentra, devuelve un mensaje de error con un código HTTP 404.
     * Si se encuentra, elimina el parámetro y devuelve un mensaje de éxito en formato JSON.
     *
     * @param string $id El identificador único del parámetro de configuración a eliminar.
     */
    public function destroy(string $id)
    {
        $configuracion = Configuracion::find($id);

        if (!$configuracion) {
            return response()->json([
                'message' => 'Parametro no encontrado.'
            ], 404);
        }

        $configuracion->delete();

        return response()->json([
            'message' => 'Parametro eliminado con éxito.'
        ]);
    }

}
