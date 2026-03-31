<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\frontend\frontendController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\SalesController;
use App\Models\Doctor;

Route::get('/', [frontendController::class, 'index'])->name('home');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/home',[HomeController::class,'redirect']);

//admin routes

Route::get('/add_doctor_view', [AdminController::class, 'addview']);
Route::get('/index', [AdminController::class, 'index']);
Route::post('/upload_doctor', [AdminController::class, 'upload_doctor']);
Route::post('/appointment', [HomeController::class, 'appointment']);
Route::get('/show_appointment', [AdminController::class, 'show_appointment']);
Route::get('/delete_appoint/{id}', [AdminController::class, 'delete_appoint']);
Route::get('/update_appoint/{id}', [AdminController::class, 'update_appoint']);
Route::post('/appointment_update/{id}', [AdminController::class, 'appointment_update']);
Route::get('/view_doctor', [AdminController::class, 'view_doctor']);
Route::get('/delete_doctor/{id}', [AdminController::class, 'delete_doctor']);
Route::get('/show_doctor/{id}', [AdminController::class, 'show_doctor']);
Route::post('/edit_doctor/{id}', [AdminController::class, 'edit_doctor']);
Route::get('/add_appointment', [AdminController::class, 'add_appointment']);

//patient routes
Route::get('/add_appointment_view', [PatientController::class, 'addview']);
Route::get('/add_appointment_view', [PatientController::class, 'index']);
Route::get('/my_appointment', [PatientController::class, 'my_appointment']);
Route::get('/cancel_appoint/{id}', [PatientController::class, 'cancel_appoint']);

//Doctors routes

Route::get('/doctor_appointment', [DoctorController::class, 'doctor_appointment']);
Route::get('/update/{id}', [DoctorController::class, 'update']);
Route::post('/update_appointment/{id}', [DoctorController::class, 'update_appointment']);

//Pharmacist routes
Route::get('/view_medicine', [PharmacyController::class, 'view_medicine']);
Route::post('/upload_medicine', [PharmacyController::class, 'upload_medicine']);
Route::get('/show_medicine', [PharmacyController::class, 'show_medicine']);
Route::get('/delete_medicine/{id}', [PharmacyController::class, 'delete_medicine']);
Route::get('/edit_medicine/{id}', [PharmacyController::class, 'edit_medicine']);
Route::post('/update_medicine/{id}', [PharmacyController::class, 'update_medicine']);
//inventory
Route::get('/Add_inventory', [InventoryController::class, 'Add_inventory']);
Route::post('/add_inventory', [InventoryController::class, 'inventory']);
Route::get('/inventory/stock/{medicine}', [InventoryController::class, 'getCurrentStock']);
Route::get('/view_inventory', [InventoryController::class, 'view_inventory']);
Route::get('/delete_inventory/{id}', [InventoryController::class, 'delete_inventory']);
Route::get('edit_inventory/{id}', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::post('update_inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
Route::get('/inventory/stock/{medicine_id}', [InventoryController::class, 'getStockPrice']);

//sales
//Route::get('/add_sales', [SalesController::class, 'create']);
//Route::post('/add_sale', [SalesController::class, 'add_sale']);