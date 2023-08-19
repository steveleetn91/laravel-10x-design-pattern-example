<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::prefix('/dashboard')->group(function(){
    Route::middleware(['auth', 'verified'])->group(function(){
        Route::get('/',function(){
            return Inertia::render('Dashboard');
        })->name('dashboard');
        Route::prefix('/users')->group(function(){
            Route::get('/','App\Http\Controllers\Users\UsersController@index')->name('users-list');
            Route::get('/create','App\Http\Controllers\Users\UsersController@create')->name('users-create');
            Route::get('/{id}','App\Http\Controllers\Users\UsersController@edit')->name('users-edit');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
