<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeopleController;

// Rotas de autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

// Rotas de pessoas - CRUD completo
Route::prefix('people')->group(function () {
    // Listar todas as pessoas (com filtros opcionais)
    Route::get('/', [PeopleController::class, 'index']);

    // Criar nova pessoa
    Route::post('/', [PeopleController::class, 'store']);

    // Buscar pessoas
    Route::get('/search', [PeopleController::class, 'search']);

    // Operações específicas por ID
    Route::get('/{id}', [PeopleController::class, 'show']);
    Route::put('/{id}', [PeopleController::class, 'update']);
    Route::delete('/{id}', [PeopleController::class, 'destroy']);
});


