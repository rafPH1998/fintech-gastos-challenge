<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::post('/registrar', [AuthController::class, 'registerUser']);
Route::post('/entrar', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sair', [AuthController::class, 'logout']);
    Route::get('/usuario', [AuthController::class, 'getUserLogado']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/categorias', [ExpenseCategoryController::class, 'listar']);
    Route::post('/categorias', [ExpenseCategoryController::class, 'criar']);
    Route::put('/categorias/{category}', [ExpenseCategoryController::class, 'atualizar']);
    Route::delete('/categorias/{category}', [ExpenseCategoryController::class, 'excluir']);

    Route::get('/despesas', [ExpenseController::class, 'listar']);
    Route::post('/despesas', [ExpenseController::class, 'criar']);
    Route::put('/despesas/{expense}', [ExpenseController::class, 'atualizar']);
    Route::delete('/despesas/{expense}', [ExpenseController::class, 'excluir']);
});
