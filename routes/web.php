<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserController::class, 'index']);
Route::get('/usuarios/listar', [UserController::class, 'usuarios']);
Route::get('/usuarios/{id}/editar', [UserController::class, 'edit']);

Route::post('/usuarios/salvar', [UserController::class, 'store']);
Route::post('/usuarios/{id}/atualizar', [UserController::class, 'update']);

Route::delete('/usuarios/{id}/excluir', [UserController::class, 'destroy']);