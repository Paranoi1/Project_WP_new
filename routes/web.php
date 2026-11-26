<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ModuleController;
Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
    
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
Route::group([
    'prefix' => 'admin', 
    'as' => 'admin.', 
    'middleware' => ['auth', 'admin']], function () {
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
});
  */  
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    
    ->group(function () {
        Route::get('/classes', [ClassController::class, 'index'])
            ->name('classes.index');

        Route::post('/classes', [ClassController::class, 'store'])
            ->name('classes.store');
            
        Route::get('/classes/{courseId}/modules', [ModuleController::class, 'index'])
            ->name('classes.modules.index');

        Route::post('/classes/{courseId}/modules', [ModuleController::class, 'store'])
            ->name('classes.modules.store');
    });

require __DIR__.'/auth.php';