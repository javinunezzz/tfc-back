<?php

use App\Http\Controllers\Admin\ApunteAdminController;
use App\Http\Controllers\Admin\AsignaturaAdminController;
use App\Http\Controllers\Admin\CategoriaAdminController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\DescargaAdminController;
use App\Http\Controllers\Admin\SuscripcionAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\ApunteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\FavoritoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

/**
 * Ruta para obtener el usuario autenticado
 */
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =======================================
// =============== ADMIN ================
// =======================================

// --------- Usuarios (solo ADMIN puede crear, buscar) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/usuarios', [UserAdminController::class, 'index']);
    Route::post('/usuarios', [UserAdminController::class, 'store']);
    Route::post('/usuarios/buscar-username', [UserAdminController::class, 'searchByUsername']);
    Route::post('/usuarios/buscar-email', [UserAdminController::class, 'searchByEmail']);
});

// --------- Usuarios (ADMIN, PREMIUM, FREE pueden ver, actualizar y borrar) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/usuarios/{id}', [UserAdminController::class, 'show']);
    Route::put('/usuarios/{id}', [UserAdminController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserAdminController::class, 'destroy']);
});

// --------- Registro, login, email y password (sin middleware de rol) ---------
Route::post('/register', [UserAdminController::class, 'register']);
Route::post('/verify-email', [UserAdminController::class, 'verifyEmail']);
Route::post('/login', [UserAdminController::class, 'login']);
Route::post('/reset-password-request', [UserAdminController::class, 'resetPasswordRequest']);
Route::get('/validar-token', [UserAdminController::class, 'validarToken']);
Route::post('/reset-password', [UserAdminController::class, 'resetPassword']);

// --------- Categorías (solo ADMIN) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/categorias', [CategoriaAdminController::class, 'index']);
    Route::get('/categorias/{id}', [CategoriaAdminController::class, 'show']);
    Route::post('/categorias', [CategoriaAdminController::class, 'store']);
    Route::put('/categorias/{id}', [CategoriaAdminController::class, 'update']);
    Route::delete('/categorias/{id}', [CategoriaAdminController::class, 'destroy']);
});

// --------- Asignaturas (solo ADMIN) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/asignaturas', [AsignaturaAdminController::class, 'index']);
    Route::get('/asignaturas/{id}', [AsignaturaAdminController::class, 'show']);
    Route::post('/asignaturas', [AsignaturaAdminController::class, 'store']);
    Route::put('/asignaturas/{id}', [AsignaturaAdminController::class, 'update']);
    Route::delete('/asignaturas/{id}', [AsignaturaAdminController::class, 'destroy']);
});

// --------- Apuntes (solo ADMIN) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/apuntes', [ApunteAdminController::class, 'index2']);
    Route::get('/apuntes/{id}', [ApunteAdminController::class, 'show2']);
    Route::post('/apuntes', [ApunteAdminController::class, 'store']);
    Route::put('/apuntes/{id}', [ApunteAdminController::class, 'update']);
    Route::delete('/apuntes/{id}', [ApunteAdminController::class, 'destroy']);
    Route::post('/apuntesBuscar', [ApunteAdminController::class, 'buscar']);
    Route::post('/apuntesBuscarUsuario', [ApunteAdminController::class, 'buscarPorUsername']);
});

// Rutas para descarga de apuntes y PDF
Route::get('/apuntes/{id}/pdf', [ApunteAdminController::class, 'download']);
Route::get('pdf/{fileName}', [PdfController::class, 'show']);

// --------- Descargas (solo ADMIN) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/descargas', [DescargaAdminController::class, 'index']);
    Route::get('/descargas/{id}', [DescargaAdminController::class, 'show']);
    Route::post('/descargas', [DescargaAdminController::class, 'store']);
    Route::put('/descargas/{id}', [DescargaAdminController::class, 'update']);
    Route::delete('/descargas/{id}', [DescargaAdminController::class, 'destroy']);
});

// --------- Suscripciones (solo ADMIN) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN'])->group(function () {
    Route::get('/suscripciones', [SuscripcionAdminController::class, 'index']);
    Route::get('/suscripciones/{id}', [SuscripcionAdminController::class, 'show']);
    Route::post('/suscripciones', [SuscripcionAdminController::class, 'store']);
    Route::put('/suscripciones/{id}', [SuscripcionAdminController::class, 'update']);
    Route::delete('/suscripciones/{id}', [SuscripcionAdminController::class, 'destroy']);
});

// --------- Configuración (ADMIN, PREMIUM, FREE) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/configuracion/{id}', [ConfiguracionController::class, 'show']);
    Route::delete('/configuracion/{id}', [ConfiguracionController::class, 'destroy']);
});

Route::get('/configuracion', [ConfiguracionController::class, 'getParametrosConfiguracion']);
Route::get('/configuracion-valor/{parametro}', [ConfiguracionController::class, 'getValorPorParametro']);
Route::put('/configuracion/{parametro}', [ConfiguracionController::class, 'setParametrosConfiguracion']);


// =======================================
// ============== USUARIO ================
// =======================================

// --------- Apuntes (ADMIN, PREMIUM, FREE) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/apunte/filtrar/{categoria_id}/{asignatura_id}', [ApunteController::class, 'getApuntesByFilters']);
    Route::get('/apunte/mis-apuntes', [ApunteController::class, 'misApuntes']);
    Route::post('/apunte', [ApunteController::class, 'store']);
    Route::put('/apunte/{id}', [ApunteController::class, 'update']);
    Route::delete('/apunte/{id}', [ApunteController::class, 'destroy']);
    Route::get('/apunte/descargar/{id}', [ApunteController::class, 'download']);
    Route::get('/apunte/buscar/{categoria_nombre}/{asignatura_id}', [ApunteController::class, 'buscar']);
    Route::get('/apunte/recientes', [ApunteController::class, 'recientes']);
    Route::get('/apunte/mis-estadisticas', [ApunteController::class, 'misEstadisticas']);
});

// --------- Categorías (ADMIN, PREMIUM, FREE) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/categoria', [CategoriaController::class, 'index']);
    Route::get('/categoriaID/{id}', [CategoriaController::class, 'showById']);
    Route::get('/categoriaNombre/{nombre}', [CategoriaController::class, 'showByNombre']);
});

// --------- Asignaturas (ADMIN, PREMIUM, FREE) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/asignatura', [AsignaturaAdminController::class, 'index']);
    Route::get('/asignatura/{id}', [AsignaturaAdminController::class, 'show']);
});

// --------- Suscripciones (ADMIN, PREMIUM, FREE) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM|FREE'])->group(function () {
    Route::get('/suscripcione', [SuscripcionController::class, 'index']);
    Route::get('/suscripcione/{id}', [SuscripcionController::class, 'show']);
    Route::post('/suscripcione', [SuscripcionController::class, 'store']);
    Route::put('/suscripcione/{id}', [SuscripcionController::class, 'update']);
    Route::delete('/suscripcione/{id}', [SuscripcionController::class, 'destroy']);
});

// --------- Favoritos (ADMIN, PREMIUM) ---------
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':ADMIN|PREMIUM'])->group(function () {
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos', [FavoritoController::class, 'store']);
    Route::delete('/favoritos/{id}', [FavoritoController::class, 'destroy']);
});
