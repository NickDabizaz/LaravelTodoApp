<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;


// Home Page 
Route::get('/', [HomeController::class, 'index'])->name("home");
Route::post('/todos', [HomeController::class, 'store'])->name('todos.store');
Route::post('/todos/{todo}/done', [HomeController::class, 'markAsDone'])->name('todos.done');
Route::delete('/todos/{todo}', [HomeController::class, 'delete'])->name('todos.delete');
Route::put('/todos/{id}', [HomeController::class, 'update'])->name('todos.update');

// Login Page
Route::get('/login', [AuthController::class, 'index'])->name("login");
Route::post('/login', [AuthController::class, 'login']);

//Register Page
Route::get('/register', [AuthController::class, 'toRegister'])->name("register");
Route::post('/register', [AuthController::class, 'registerNewAccount']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name("logout");
