<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');

// Route::get('/meetup', [App\Http\Controllers\MeetupController::class, 'index'])->name('meetup.index');
Route::view('/meetup', 'meetup.index')->name('meetup.index');
Route::get('/meetup/create', [App\Http\Controllers\MeetupController::class, 'create'])->name('meetup.create');
Route::post('/meetup', [App\Http\Controllers\MeetupController::class, 'store'])->name('meetup.store');

Route::get('/news', [App\Http\Controllers\NewsController::class, 'index'])->name('news.index');

Route::get('/price-history', [App\Http\Controllers\CoinbasePriceController::class, 'index'])->name('price.history.index');
