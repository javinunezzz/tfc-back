<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserAdminController extends Controller
{
    /**
     * Mostrar todos los usuarios.
     *
     * @param Request $request Solicitud HTTP.
     */
    public function index(Request $request)
    {
        // Recuperar todos los usuarios sin aplicar orden
        $usuarios = User::all();

        // Verificar si se encontraron usuarios
        if ($usuarios->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron usuarios.'
            ], 404);
        }

        // Si se encontraron usuarios, retornar respuesta exitosa
        return response()->json($usuarios);
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param Request $request Solicitud HTTP con datos del usuario.
     */
    public function store(Request $request)
    {
        try {
            // Validaciones
            $request->validate([
                'name' => 'required|string|max:50',
                'username' => 'required|string|max:20|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:4|max:30',
            ]);

            DB::beginTransaction();
            // Crear un nuevo usuario
            $user = User::create([
                'name' => $request['name'],
                'username' => $request['username'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'plan' => $request['plan'],
                'rol' => $request['rol'],
            ]);
            DB::commit();
            // Responder con éxito y devolver el nuevo usuario creado
            return response()->json($user, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // En caso de error, retornar un mensaje de error
            return response()->json([
                'message' => 'Hubo un error al crear el usuario.' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar los datos de un usuario específico.
     *
     * @param string $id Identificador del usuario.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualizar los datos de un usuario existente.
     *
     * @param Request $request Solicitud HTTP con datos a actualizar.
     * @param string $id Identificador del usuario.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        try {
            // Validaciones
            $request->validate([
                'name' => 'sometimes|required|string|max:50',
                'username' => 'sometimes|required|string|max:20|unique:users,username,' . $id,
            ]);

            DB::beginTransaction();

            $user->update([
                'name' => $request->input('name', $user->name),
                'username' => $request->input('username', $user->username),
                'plan' => $request->input('plan', $user->plan),
                'rol' => $request->filled('rol') ? $request->input('rol') : $user->rol,
            ]);

            // Solo asignamos el rol si se ha enviado en la solicitud y no está vacío
            if ($request->filled('rol')) {
                $user->roles()->detach();
                $user->assignRole($request->input('rol'));
            }

            DB::commit();
            return response()->json($user);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error al actualizar el usuario. ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Eliminar un usuario.
     *
     * @param string $id Identificador del usuario a eliminar.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        try {
            DB::beginTransaction();
            $user->delete();
            DB::commit();
            return response()->json([
                'message' => 'Usuario eliminado con éxito.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error al eliminar el usuario. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar un nuevo usuario y enviar correo de verificación.
     *
     * @param Request $request Solicitud HTTP con datos del usuario.
     */
    public function register(Request $request)
    {
        try {
            // Validaciones
            $request->validate([
                'name' => 'required|string|max:50',
                'username' => 'required|string|max:20|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:4|max:30',
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $codigo = Str::random(20);
            $user->codigo = $codigo;
            $user->save();

            DB::commit();

            // Generamos el link para verificar el correo que redirige al frontend
            $verificationLink = env('FRONTEND_URL') . '/auth/verificar-email?email=' . urlencode($user->email) . '&code=' . $codigo;

            // Mandamos el correo de verificación
            Mail::send('emails.verify-email', ['verificationLink' => $verificationLink], function ($message) use ($user) {
                $message->to($user->email);
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->subject('Verifica tu correo electrónico');
            });

            return response()->json([
                'message' => 'Usuario creado. Por favor, verifica tu correo electrónico.',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error al registrar el usuario. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar el correo electrónico del usuario mediante código.
     *
     * @param Request $request Solicitud HTTP con email y código de verificación.
     */
    public function verifyEmail(Request $request)
    {
        try {
            $email = $request->input('email');
            $codigo = $request->input('code');

            $user = User::where('email', $email)->where('codigo', $codigo)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Código de verificación inválido.'
                ], 400);
            }

            $user->email_verified_at = now();
            $user->codigo = null;
            // Le asigna el rol por defecto
            $user->assignRole("FREE");
            $user->rol = 'FREE';
            $user->save();

            return response()->json([
                'message' => 'Correo electrónico verificado correctamente.',
                'redirectUrl' => env('FRONTEND_URL') . '/auth/login'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al verificar el correo electrónico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar sesión con email y contraseña.
     *
     * @param Request $request Solicitud HTTP con credenciales.
     */
    public function login(Request $request)
    {
        // Verificar si el usuario existe
        if (!($request)) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        // Verificar si las credenciales son correctas
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $user = Auth::user();

        // Verificar si el correo ha sido verificado
        if ($user->email_verified_at === null) {
            return response()->json([
                'message' => 'El correo electrónico no ha sido verificado.'
            ], 403);
        }


        $token = $user->createToken('Castelar')->plainTextToken;
        //$token = $user->createToken('Castelar', ['role:admin', 'role:emple', 'level:1', 'Administrador'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'id' => $user->id,
            ],
            'roles' => $user->getRoleNames(),
        ], 200);
    }

    /**
     * Solicitar restablecimiento de contraseña enviando correo con enlace.
     *
     * @param Request $request Solicitud HTTP con email del usuario.
     */
    public function resetPasswordRequest(Request $request)
    {
        // Validar que el email existe en la base de datos
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // Verificar si ya existe un token de restablecimiento para este correo y eliminarlo
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Generar el token de reset
        $token = Str::random(60);

        // Guardar el nuevo token en la base de datos
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Generar el enlace de restablecimiento
        $resetLink = env('FRONTEND_URL') . "/auth/cambio-contrasena?email=" . ($user->email) . "&token=" . $token;

        // Enviar el correo con el enlace
        Mail::send('emails.reset-password', ['resetLink' => $resetLink], function ($message) use ($user) {
            $message->to($user->email);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $message->subject('Restablecimiento de contraseña');
        });

        // Responder de forma exitosa
        return response()->json(['message' => 'Se ha enviado un enlace de restablecimiento a su correo.']);
    }

    /**
     * Validar token de restablecimiento de contraseña.
     *
     * @param Request $request Solicitud HTTP con email y token.
     */
    public function validarToken(Request $request)
    {
        // Validar los datos recibidos por la URL
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required'
        ]);

        // Verificar si el token existe en la base de datos
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['message' => 'Token inválido o expirado.'], 400);
        }

        return response()->json(['message' => 'Token válido.'], 200);
    }

    /**
     * Restablecer la contraseña usando token válido.
     *
     * @param Request $request Solicitud HTTP con email, token y nueva contraseña.
     */
    public function resetPassword(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar si el token de restablecimiento existe en la base de datos
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['message' => 'El token de restablecimiento no es válido o ha expirado.'], 400);
        }

        // Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // Actualizar la contraseña del usuario
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el token de la base de datos después de usarlo
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Responder de forma exitosa
        return response()->json(['message' => 'Contraseña restablecida con éxito.']);
    }


    /**
     * Buscar usuarios cuyo nombre de usuario coincida parcialmente con el término dado.
     *
     * @param Request $request Solicitud HTTP con parámetro 'username'.
     */
    public function searchByUsername(Request $request)
    {
        try {
            $username = $request->input('username');

            $usuarios = User::where('username', 'LIKE', "%{$username}%")->get();

            if ($usuarios->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron usuarios con ese nombre de usuario.'
                ], 404);
            }

            return response()->json($usuarios);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar usuarios cuyo correo electrónico coincida parcialmente con el término dado.
     *
     * @param Request $request Solicitud HTTP con parámetro 'email'.
     */
    public function searchByEmail(Request $request)
    {
        try {
            $email = $request->input('email');

            $usuarios = User::where('email', 'LIKE', "%{$email}%")->get();

            if ($usuarios->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron usuarios con ese correo electrónico.'
                ], 404);
            }

            return response()->json($usuarios);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

}
