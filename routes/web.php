<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $posts = Post::published()->get();

    return view('welcome', compact('posts'));
})->name('welcome');

Route::get('/posts', function () {
    $posts = Post::all()->sortByDesc('publish_date');

    return view('posts.index', compact('posts'));
})->name('posts.index');

Route::get('/posts/show/{id}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/edit/{id}', [PostController::class, 'edit'])->name('posts.edit');
Route::post('/posts/edit/{id}', [PostController::class, 'update'])->name('posts.update');
Route::post('/posts/delete/{id}', [PostController::class, 'destroy'])->name('posts.destroy');

Route::get('/dashboard', function () {

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('posts/store', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');

});

require __DIR__.'/auth.php';
