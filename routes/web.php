<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibraryController;

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

Route::get('/', [LibraryController::class, 'home'])->name('home');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LibraryController::class, 'showLogin'])->name('login');
    Route::post('/login', [LibraryController::class, 'login'])->name('login.submit');
    Route::get('/daftar', [LibraryController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [LibraryController::class, 'register'])->name('register.submit');
});
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::post('/buku/{book}/pinjam', [LibraryController::class, 'borrow'])->name('books.borrow');
    Route::get('/peminjaman-saya', [LibraryController::class, 'myLoans'])->name('my-loans');
    Route::post('/peminjaman/{loan}/kembalikan', [LibraryController::class, 'returnBook'])->name('loans.return');
    Route::post('/logout', [LibraryController::class, 'logout'])->name('logout');
});
