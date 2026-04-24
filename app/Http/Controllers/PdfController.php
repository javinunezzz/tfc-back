<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    /**
     * Muestra un archivo PDF solicitado por nombre.
     *
     * Busca el archivo PDF en el almacenamiento dentro de la ruta
     * "/upload_noteshare/pdf/{fileName}". Si no existe, devuelve un PDF
     * por defecto ubicado en la carpeta pública.
     *
     * @param string $fileName Nombre del archivo PDF a mostrar.
     */
    public function show($fileName)
    {
        $path = config('app.upload_pdf') . $fileName;

        if (Storage::exists($path)) {
            // Devuelve el archivo PDF solicitado si existe
            return response()->file(Storage::path($path));
        }

        // Archivo fallback si no se encuentra el PDF solicitado
        $fallback = public_path('pdf/pdf_no_disponible.pdf');

        return response()->file($fallback);
    }
}
