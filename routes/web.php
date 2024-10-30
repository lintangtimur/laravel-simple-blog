<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/posts', function () {
    return view('posts.index');
});

Route::get('/posts/create', function () {
    return view('posts.create');
});

Route::get('/posts/show', function () {
    return view('posts.show');
});

Route::get('/posts/edit', function () {
    return view('posts.edit');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::post('posts/store', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/form' , [PostController::class, 'createForm'])->name('posts.create');
    Route::get('posts/{id}', [PostController::class, 'show'])->name('posts.show');
});

require __DIR__.'/auth.php';
