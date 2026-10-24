<?php

use App\Http\Controllers\BeritaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('berita.index');
});

Route::resource('berita', BeritaController::class)
    ->parameters(['berita' => 'berita']);
