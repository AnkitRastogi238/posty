<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/users/{user:username}/posts', [UserPostController::class, 'index'])->name('users.posts');

Route::post('/logout', [LogoutController::class, 'store'])->name('logout');

Route::get('/login', [LoginController::class, 'index'])->name('user.login');
Route::post('/login/validate', [LoginController::class, 'validateLogin'])->name('user.validate');
Route::get('/user/verify/{token}', [LoginController::class, 'verifyEmail'])->name('user.verify');
Route::get('/forget-password', [LoginController::class, 'getForgetPassword'])->name('getForgetPassword');
Route::post('/forget-password', [LoginController::class, 'postForgetPassword'])->name('postForgetPassword');
Route::get('/reset-password/{reset_code}', [LoginController::class, 'getResetPassword'])->name('getResetPassword');
Route::post('/reset-password/{reset_code}', [LoginController::class, 'postResetPassword'])->name('postResetPassword');


Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);


Route::get('/posts', [PostController::class, 'index'])->name('posts');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts', [PostController::class, 'store']);
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

Route::post('/posts/{post}/likes', [PostLikeController::class, 'store'])->name('posts.likes');
Route::delete('/posts/{post}/likes', [PostLikeController::class, 'destroy'])->name('posts.likes');
