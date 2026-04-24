<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Apunte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class FavoritoController extends Controller
{
    /**
     * Obtener todos los favoritos del usuario autenticado con información detallada.
     */
    public function index()
    {
        $favoritos = Favorito::with([
            'apunte.user:id,name,username',
            'apunte.categoria:id,nombre',
            'apunte.asignatura:id,nombre',
            'apunte:id,categoria_id,asignatura_id,titulo,descripcion,pdf,user_id',
            'user:id,name,username'
        ])
            ->where('user_id', Auth::id())
            ->get()
            ->map(function ($favorito) {
                $apunte = $favorito->apunte;

                return [
                    'id' => $favorito->id,
                    'user_id' => $favorito->user_id,
                    'apunte_id' => $favorito->apunte_id,
                    'apunte' => [
                        'id' => $apunte->id,
                        'categoria_id' => $apunte->categoria_id,
                        'asignatura_id' => $apunte->asignatura_id,
                        'titulo' => $apunte->titulo,
                        'descripcion' => $apunte->descripcion,
                        'pdf' => $apunte->pdf,
                        'user' => [
                            'id' => $apunte->user->id,
                            'name' => $apunte->user->name,
                            'username' => $apunte->user->username,
                        ],
                        'categoria' => [
                            'id' => $apunte->categoria->id,
                            'nombre' => $apunte->categoria->nombre,
                        ],
                        'asignatura' => [
                            'id' => $apunte->asignatura->id,
                            'nombre' => $apunte->asignatura->nombre,
                        ],
                    ]
                ];
            });

        if ($favoritos->isEmpty()) {
            return response()->json([], 200);
        }

        return response()->json($favoritos);
    }

    /**
     * Guardar un nuevo favorito para el usuario autenticado.
     * Solo usuarios con rol 'PREMIUM' o 'ADMIN' pueden guardar favoritos.
     * Envía un email al propietario del apunte.
     *
     * @param Request $request Datos enviados por el cliente, se espera 'apunte_id'.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->rol !== 'PREMIUM' && $user->rol !== 'ADMIN') {
                return response()->json([
                    'message' => 'Solo los usuarios Premium pueden guardar favoritos.'
                ], 403);
            }

            $apunte = Apunte::with(['categoria:id,nombre', 'asignatura:id,nombre'])
                ->find($request->apunte_id);
            if (!$apunte) {
                return response()->json([
                    'message' => 'Apunte no encontrado.'
                ], 404);
            }

            $favorito = Favorito::create([
                'user_id' => $user->id,
                'apunte_id' => $request->apunte_id
            ]);

            // Enviar email al propietario del apunte
            $apunteOwner = User::find($apunte->user_id);
            Mail::send('emails.favorito', [
                'username' => $user->username,
                'apunte' => $apunte->titulo,
                'categoria' => $apunte->categoria->nombre,
                'asignatura' => $apunte->asignatura->nombre
            ], function($message) use ($apunteOwner) {
                $message->to($apunteOwner->email);
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->subject('¡Nuevo favorito en tus apuntes!');
            });

            return response()->json($favorito, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar favorito: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un favorito por su ID, solo si pertenece al usuario autenticado.
     *
     * @param int|string $id ID del favorito a eliminar.
     */
    public function destroy($id)
    {
        try {
            $favorito = Favorito::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$favorito) {
                return response()->json([
                    'message' => 'Favorito no encontrado.'
                ], 404);
            }

            $favorito->delete();

            return response()->json([
                'message' => 'Favorito eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar favorito: ' . $e->getMessage()
            ], 500);
        }
    }
}
