<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', function () {
    // Handle login logic here
})->name('login.post');

Route::get('register', function () {
    return view('auth.register');
})->name('register');

Route::post('register', function () {
    // Handle registration logic here
})->name('register.post');

Route::get('/share/{shareId}', [ShareController::class, 'viewShare'])->name('share.view');

Route::post('/share/{shareId}/password', [ShareController::class, 'checkPassword'])
    ->name('share.password');

Route::get('/share/{shareId}/download/{fileId}', [FileController::class, 'downloadFile'])
    ->name('share.file.download');
Route::post('/api/share', [ShareController::class, 'createShare'])->name('share.create');
Route::post('/api/share/{shareId}/files', [FileController::class, 'uploadFiles']);

