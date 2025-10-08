<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationController;
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


//defalut route onload 
Route::get('/', [StudentController::class, 'index'])->name('index');

//users route
Route::get('/user/index', [UserController::class, 'index'])->name('userIndex');
Route::get('/user/create', function () {
    return view('users.create');
});
Route::post('/user/create', [UserController::class, 'create'])->name('userCreate');
Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('userEdit');
Route::put('/user/update/{id}', [UserController::class, 'update'])->name('userUpdate');
Route::get('/user/delete/{id}', [UserController::class, 'delete'])->name('userDelete');

//Students Route
Route::get('/students/upload', [StudentController::class, 'showUploadForm'])->name('students.upload.form');
Route::post('/students/upload', [StudentController::class, 'uploadCsv'])->name('students.upload.csv');
Route::resource('students', StudentController::class);


//images direct upload to db 
Route::get('/images', [ImageController::class, 'index'])->name('images.index');
Route::post('/images', [ImageController::class, 'store'])->name('images.store');


//for notification
Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
