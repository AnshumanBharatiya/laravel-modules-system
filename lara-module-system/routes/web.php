<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModulesController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('modules')->group(function () {
    Route::get('/', [ModulesController::class, 'modules']);
    // manage modules.
    Route::get('/enable/{moduleName}', [ModulesController::class, 'enable'])->name('modules.enable');
    Route::get('/disable/{moduleName}', [ModulesController::class, 'disable'])->name('modules.disable');
    Route::get('/export/{moduleName}', [ModulesController::class, 'export'])->name('modules.export');
    Route::get('/delete/{moduleName}', [ModulesController::class, 'delete'])->name('modules.delete');

    // upload zip files. 
    Route::get('/upload', [ModulesController::class, 'upload'])->name('modules.upload');
    Route::post('/upload/zip', [ModulesController::class, 'uploadZip'])->name('upload.zip');
});