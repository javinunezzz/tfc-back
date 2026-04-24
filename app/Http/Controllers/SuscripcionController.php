<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class SuscripcionController extends Controller
{

    /**
     * Muestra todas las suscripciones con datos del usuario.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $suscripciones = Suscripcion::with(['user:id,name'])->get();

        if ($suscripciones->isEmpty()) {
            return response()->json([], 200);
        }

        // Oculta los IDs redundantes.
        $suscripciones->makeHidden(['user_id']);

        return response()->json($suscripciones);
    }

    /**
     * Crea una nueva suscripción para el usuario autenticado,
     * asigna rol PREMIUM y envía factura por email en PDF.
     *
     * @return JsonResponse
     */
    public function store()
    {
        try {
            $userId = auth()->id();

            if (!$userId) {
                return response()->json([
                    'message' => 'Usuario no autenticado.'
                ], 401);
            }

            DB::beginTransaction();
            try {
                // Establecer la fecha de inicio como la fecha actual y la fecha de fin un mes después
                $fechaInicio = now();
                $fechaFin = now()->addMonth();
                $estado = 'activo';

                // Crear la suscripción
                $suscripciones = Suscripcion::create([
                    'user_id' => $userId,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'estado' => $estado,
                    'paypal_subscription_id' => request('paypal_subscription_id'),
                ]);

                // Obtener el usuario logueado
                $user = User::where('id', $userId)->first();
                $user->roles()->detach();
                $user->assignRole("PREMIUM");
                $user->rol = 'PREMIUM';
                $user->plan = 'premium';
                $user->save();

                // Generar y enviar la factura PDF
                $pdf = PDF::loadView('emails.factura', [
                    'user' => $user,
                    'suscripcion' => $suscripciones
                ]);

                // Enviar el correo con la factura adjunta
                Mail::send('emails.factura', [
                    'user' => $user,
                    'suscripcion' => $suscripciones
                ], function($message) use ($user, $pdf) {
                    $message->to($user->email);
                    $message->subject('Factura de tu suscripción Premium');
                    $message->attachData($pdf->output(), 'factura.pdf');
                });

                DB::commit();

                // Formatear las fechas antes de devolverlas
                $suscripciones->fecha_inicio = $suscripciones->fecha_inicio->format('Y-m-d H:i:s');
                $suscripciones->fecha_fin = $suscripciones->fecha_fin->format('Y-m-d H:i:s');

                return response()->json($suscripciones, 201);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al crear la suscripción. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la suscripción de un usuario específico.
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function show(string $userId)
    {
        $suscripcion = Suscripcion::with(['user:id,name'])
                        ->where('user_id', $userId)
                        ->first();

        if (!$suscripcion) {
            return response()->json([
                'message' => 'No se encontró suscripción para este usuario.'
            ], 404);
        }

        // Oculta los IDs redundantes.
        $suscripcion->makeHidden(['user_id']);

        return response()->json($suscripcion);
    }

    /**
     * Actualiza los datos de una suscripción específica.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        $suscripcion = Suscripcion::find($id);

        if (!$suscripcion) {
            return response()->json([
                'message' => 'No se encontró la suscripcion.'
            ], 404);
        }

        try {
            $suscripcion->update([
                'user_id' => $request->input('user_id', $suscripcion->user_id),
                'fecha_inicio' => $request->input('fecha_inicio', $suscripcion->fecha_inicio),
                'fecha_fin' => $request->input('fecha_fin', $suscripcion->fecha_fin),
                'estado' => $request->input('estado', $suscripcion->estado),
                'paypal_subscription_id' => $request->input('paypal_subscription_id', $suscripcion->paypal_subscription_id),
            ]);

            return response()->json($suscripcion);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error al actualizar la suscripcion. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una suscripción y actualiza el rol del usuario a FREE.
     * También cancela la suscripción en PayPal si aplica.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $suscripcion = Suscripcion::find($id);

            if (!$suscripcion) {
                return response()->json([
                    'message' => 'No se encontró la suscripción.'
                ], 404);
            }

            // Cancelar en PayPal si existe ID de suscripción
            if (!empty($suscripcion->paypal_subscription_id)) {
                $paypalClient = new PayPalClient();
                $response = $paypalClient->cancelSubscription($suscripcion->paypal_subscription_id);

                if (!$response['success']) {
                    return response()->json([
                        'message' => 'Error al cancelar la suscripción en PayPal: ' . ($response['error'] ?? 'Error desconocido')
                    ], 500);
                }
            }

            // Obtener el usuario de la suscripción
            $user = User::find($suscripcion->user_id);

            if (!$user) {
                return response()->json([
                    'message' => 'No se encontró el usuario asociado a la suscripción.'
                ], 404);
            }

            DB::beginTransaction();
            try {
                // Actualizar usuario a FREE
                $user->roles()->detach();
                $user->assignRole("FREE");
                $user->rol = 'FREE';
                $user->plan = 'free';
                $user->save();

                // Eliminar la suscripción
                $suscripcion->delete();

                DB::commit();

                return response()->json([
                    'message' => 'Suscripción eliminada con éxito y usuario actualizado a plan FREE.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la suscripción: ' . $e->getMessage()
            ], 500);
        }
    }
}
