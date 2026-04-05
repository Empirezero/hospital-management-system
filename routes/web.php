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

// ─── Public Routes ────────────────────────────────────────────────
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

// ─── Auth Redirect (role-based) ───────────────────────────────────
Route::get('/home', [HomeController::class, 'redirect']);

// ─── Admin Routes ─────────────────────────────────────────────────
Route::get('/index', [AdminController::class, 'index'])->name('admin.home');
Route::get('/add_doctor_view', [AdminController::class, 'addview']);
Route::post('/upload_doctor', [AdminController::class, 'upload_doctor']);
Route::get('/show_appointment', [AdminController::class, 'show_appointment']);
Route::get('/delete_appoint/{id}', [AdminController::class, 'delete_appoint']);
Route::get('/update_appoint/{id}', [AdminController::class, 'update_appoint']);
Route::post('/appointment_update/{id}', [AdminController::class, 'appointment_update']);
Route::get('/view_doctor', [AdminController::class, 'view_doctor'])->name('admin.view_doctor');
Route::get('/delete_doctor/{id}', [AdminController::class, 'delete_doctor']);
Route::get('/show_doctor/{id}', [AdminController::class, 'show_doctor']);
Route::post('/edit_doctor/{id}', [AdminController::class, 'edit_doctor']);
Route::get('/add_appointment', [AdminController::class, 'add_appointment']);
// ─── User Management ──────────────────────────────────────────────
Route::get('/view_users',        [AdminController::class, 'view_users'])->name('admin.view_users');
Route::get('/add_user',          [AdminController::class, 'add_user_view'])->name('admin.add_user');
Route::post('/store_user',       [AdminController::class, 'store_user'])->name('admin.store_user');
Route::get('/edit_user/{id}',    [AdminController::class, 'edit_user_view'])->name('admin.edit_user');
Route::post('/update_user/{id}', [AdminController::class, 'update_user'])->name('admin.update_user');
Route::get('/delete_user/{id}',  [AdminController::class, 'delete_user'])->name('admin.delete_user');
// ─── Profile Routes ───────────────────────────────────────────────
Route::get('/profile',         [AdminController::class, 'profile'])->name('profile');
Route::post('/profile/update', [AdminController::class, 'update_profile'])->name('profile.update');

// ─── Appointment ──────────────────────────────────────────────────
Route::post('/appointment', [HomeController::class, 'appointment']);

// ─── Patient Routes ───────────────────────────────────────────────
Route::get('/doctor_index', [DoctorController::class, 'index'])->name('doctor.home');
Route::get('/add_appointment_view', [PatientController::class, 'addview']);
Route::get('/my_appointment', [PatientController::class, 'my_appointment']);
Route::get('/cancel_appoint/{id}', [PatientController::class, 'cancel_appoint']);

// ─── Doctor Routes ────────────────────────────────────────────────
Route::get('/doctor_index', [DoctorController::class, 'index'])->name('doctor.home');
Route::get('/doctor_appointment', [DoctorController::class, 'doctor_appointment']);
Route::get('/update/{id}', [DoctorController::class, 'update']);
Route::post('/update_appointment/{id}', [DoctorController::class, 'update_appointment']);
// ─── Doctor Encounter & Prescription Routes ───────────────────────
Route::get('/encounters',                          [DoctorController::class, 'encounters'])->name('doctor.encounters');
Route::get('/encounter/create/{appointment_id}',   [DoctorController::class, 'create_encounter'])->name('doctor.encounter.create');
Route::post('/encounter/store',                    [DoctorController::class, 'store_encounter'])->name('doctor.encounter.store');
Route::get('/encounter/{encounter_id}/close',      [DoctorController::class, 'close_encounter'])->name('doctor.encounter.close');
Route::get('/encounter/{encounter_id}/prescribe',  [DoctorController::class, 'create_prescription'])->name('doctor.prescriptions.create');
Route::post('/encounter/{encounter_id}/prescribe', [DoctorController::class, 'store_prescription'])->name('doctor.prescriptions.store');


// ─── Pharmacist Routes ────────────────────────────────────────────
Route::get('/view_medicine', [PharmacyController::class, 'view_medicine']);
Route::post('/upload_medicine', [PharmacyController::class, 'upload_medicine']);
Route::get('/show_medicine', [PharmacyController::class, 'show_medicine'])->name('pharmacist.home');
Route::get('/delete_medicine/{id}', [PharmacyController::class, 'delete_medicine']);
Route::get('/edit_medicine/{id}', [PharmacyController::class, 'edit_medicine']);
Route::post('/update_medicine/{id}', [PharmacyController::class, 'update_medicine']);
// ─── Pharmacy Prescription Routes ─────────────────────────────────
Route::get('/pharmacy/prescriptions',     [PharmacyController::class, 'pending_prescriptions'])->name('pharmacy.prescriptions');
Route::get('/pharmacy/dispense/{id}',     [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
Route::get('/pharmacy/cancel/{id}',       [PharmacyController::class, 'cancel_prescription'])->name('pharmacy.cancel_prescription');
Route::get('/pharmacy/all_prescriptions', [PharmacyController::class, 'all_prescriptions'])->name('pharmacy.all_prescriptions');

// ─── Inventory Routes ─────────────────────────────────────────────
Route::get('/Add_inventory', [InventoryController::class, 'Add_inventory']);
Route::post('/add_inventory', [InventoryController::class, 'inventory']);
Route::get('/inventory/stock/{medicine_id}', [InventoryController::class, 'getStockPrice']);
Route::get('/view_inventory', [InventoryController::class, 'view_inventory']);
Route::get('/delete_inventory/{id}', [InventoryController::class, 'delete_inventory']);
Route::get('edit_inventory/{id}', [InventoryController::class, 'edit'])->name('inventory.edit');
Route::post('update_inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
