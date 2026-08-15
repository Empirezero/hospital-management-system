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
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\InsuranceClaimController;
use App\Http\controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForcePasswordController;
use App\Http\Controllers\NurseController;

// ─── Public Routes ────────────────────────────────────────────────
Route::get('/', [frontendController::class, 'index'])->name('home');

// ─── All Authenticated Routes ─────────────────────────────────────
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'force.password.change',
])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'redirect'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'redirect']);
    Route::post('/appointment', [HomeController::class, 'appointment']);

    // ─── Profile (any authenticated role) ──────────────────────────
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update_profile'])->name('profile.update');

    // ─── Forced Password Change (any authenticated role) ───────────
    Route::get('/force-password',  [ForcePasswordController::class, 'edit'])->name('password.force.edit');
    Route::post('/force-password', [ForcePasswordController::class, 'update'])->name('password.force.update');

    // ─── Notifications (any authenticated role) ────────────────────
    Route::get('/notifications',            [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',  [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // ─── Admin Routes ─────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/index', [AdminController::class, 'index'])->name('admin.home');
        Route::get('/add_doctor_view', [AdminController::class, 'addview']);
        Route::post('/upload_doctor', [AdminController::class, 'upload_doctor']);
        Route::get('/show_appointment', [AdminController::class, 'show_appointment'])->name('admin.appointments');
        Route::get('/delete_appoint/{id}', [AdminController::class, 'delete_appoint']);
        Route::get('/update_appoint/{id}', [AdminController::class, 'update_appoint']);
        Route::post('/appointment_update/{id}', [AdminController::class, 'appointment_update']);
        Route::get('/view_doctor', [AdminController::class, 'view_doctor'])->name('admin.view_doctor');
        Route::get('/delete_doctor/{id}', [AdminController::class, 'delete_doctor']);
        Route::get('/show_doctor/{id}', [AdminController::class, 'show_doctor']);
        Route::post('/edit_doctor/{id}', [AdminController::class, 'edit_doctor']);
        Route::get('/add_appointment', [AdminController::class, 'add_appointment']);

        // User Management
        Route::get('/view_users',        [AdminController::class, 'view_users'])->name('admin.view_users');
        Route::get('/add_user',          [AdminController::class, 'add_user_view'])->name('admin.add_user');
        Route::post('/store_user',       [AdminController::class, 'store_user'])->name('admin.store_user');
        Route::get('/edit_user/{id}',    [AdminController::class, 'edit_user_view'])->name('admin.edit_user');
        Route::post('/update_user/{id}', [AdminController::class, 'update_user'])->name('admin.update_user');
        Route::get('/delete_user/{id}',  [AdminController::class, 'delete_user'])->name('admin.delete_user');

        // Admin Lab
        Route::get('/admin/lab',          [LabController::class, 'admin_index'])->name('admin.lab.index');
        Route::get('/admin/lab/requests', [LabController::class, 'admin_requests'])->name('admin.lab.requests');

        // Schedules
        Route::get('/schedules',              [ScheduleController::class, 'index'])->name('admin.schedules.index');
        Route::get('/schedules/{doctor_id}',  [ScheduleController::class, 'manage'])->name('admin.schedules.manage');
        Route::post('/schedules/{doctor_id}', [ScheduleController::class, 'save'])->name('admin.schedules.save');
    });

    // Available slots — read-only lookup used by booking forms; any authenticated role can check availability
    Route::get('/available-slots', [ScheduleController::class, 'getAvailableSlots'])->name('schedules.slots');
    

    // ─── Patient Routes ───────────────────────────────────────────
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient_index',        [PatientController::class, 'index'])->name('patient.home');
        Route::get('/add_appointment_view', [PatientController::class, 'addview']);
        Route::get('/my_appointment',       [PatientController::class, 'my_appointment']);
        Route::get('/cancel_appoint/{id}',  [PatientController::class, 'cancel_appoint']);
        Route::get('/patient/lab/results',  [LabController::class, 'patient_results'])->name('patient.lab.results');
        Route::get('/my_claims',            [PatientController::class, 'my_claims'])->name('patient.claims');
    });

    // ─── Nurse Routes ─────────────────────────────────────────────────
    Route::middleware('role:nurse')->group(function () {
        Route::get('/nurse_index', [NurseController::class, 'index'])->name('nurse.home');
        Route::get('/nurse/admissions', [NurseController::class, 'admissions'])->name('nurse.admissions');
        Route::get('/nurse/appointments', [NurseController::class, 'appointments'])->name('nurse.appointments');
    });
    // ─── Doctor Routes ────────────────────────────────────────────
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor_index',             [DoctorController::class, 'index'])->name('doctor.home');
        Route::get('/doctor_appointment',       [DoctorController::class, 'doctor_appointment']);
        Route::get('/update/{id}',              [DoctorController::class, 'update']);
        Route::post('/update_appointment/{id}', [DoctorController::class, 'update_appointment']);
        Route::get('/encounters',               [DoctorController::class, 'encounters'])->name('doctor.encounters');
        Route::get('/encounter/create/{appointment_id}',   [DoctorController::class, 'create_encounter'])->name('doctor.encounter.create');
        Route::post('/encounter/store',                    [DoctorController::class, 'store_encounter'])->name('doctor.encounter.store');
        Route::get('/encounter/{encounter_id}/close',      [DoctorController::class, 'close_encounter'])->name('doctor.encounter.close');
        Route::get('/encounter/{encounter_id}/prescribe',  [DoctorController::class, 'create_prescription'])->name('doctor.prescriptions.create');
        Route::post('/encounter/{encounter_id}/prescribe', [DoctorController::class, 'store_prescription'])->name('doctor.prescriptions.store');

        // Doctor Lab
        Route::get('/doctor/lab/request',      [LabController::class, 'request_form'])->name('doctor.lab.request');
        Route::post('/doctor/lab/request',     [LabController::class, 'store_request'])->name('doctor.lab.store');
        Route::get('/doctor/lab/requests',     [LabController::class, 'doctor_requests'])->name('doctor.lab.requests');
        Route::get('/doctor/lab/release/{id}', [LabController::class, 'release_to_patient'])->name('doctor.lab.release');
    });

    // ─── Receptionist Routes ─────────────────────────────────────
    Route::middleware('role:receptionist,admin')->group(function () {
        Route::get('/receptionist_index', [ReceptionistController::class, 'index'])->name('receptionist.home');
        Route::get('/patients/add', [ReceptionistController::class, 'add_patient_view'])->name('receptionist.add_patient');
        Route::post('/patients', [ReceptionistController::class, 'store_patient'])->name('receptionist.store_patient');
        Route::get('/patients', [ReceptionistController::class, 'view_patients'])->name('receptionist.index');
        Route::get('/patients/{id}', [ReceptionistController::class, 'show_patient'])->name('receptionist.show_patient');
        Route::get('/patients/{id}/edit', [ReceptionistController::class, 'edit_patient_view'])->name('receptionist.edit_patient');
        Route::post('/patients/{id}/update', [ReceptionistController::class, 'update_patient'])->name('receptionist.update_patient');
    });

    // ─── Pharmacist Routes ────────────────────────────────────────
    Route::middleware('role:pharmacist')->group(function () {
        Route::get('/view_medicine',         [PharmacyController::class, 'view_medicine']);
        Route::post('/upload_medicine',      [PharmacyController::class, 'upload_medicine']);
        Route::get('/show_medicine',         [PharmacyController::class, 'show_medicine'])->name('pharmacist.home');
        Route::get('/delete_medicine/{id}',  [PharmacyController::class, 'delete_medicine']);
        Route::get('/edit_medicine/{id}',    [PharmacyController::class, 'edit_medicine']);
        Route::post('/update_medicine/{id}', [PharmacyController::class, 'update_medicine']);

        // Prescriptions
        Route::get('/pharmacy/prescriptions',     [PharmacyController::class, 'pending_prescriptions'])->name('pharmacy.prescriptions');
        Route::get('/pharmacy/dispense/{id}',     [PharmacyController::class, 'dispense'])->name('pharmacy.dispense');
        Route::get('/pharmacy/cancel/{id}',       [PharmacyController::class, 'cancel_prescription'])->name('pharmacy.cancel_prescription');
        Route::get('/pharmacy/all_prescriptions', [PharmacyController::class, 'all_prescriptions'])->name('pharmacy.all_prescriptions');

        // Inventory
        Route::get('/Add_inventory',                 [InventoryController::class, 'Add_inventory'])->name('pharmacist.inventory.create');
        Route::post('/add_inventory',                [InventoryController::class, 'inventory'])->name('pharmacist.inventory.store');
        Route::get('/inventory/stock/{medicine_id}', [InventoryController::class, 'getStockPrice'])->name('inventory.stock');
        Route::get('/view_inventory',                [InventoryController::class, 'view_inventory'])->name('pharmacist.inventory');
        Route::get('/delete_inventory/{id}',         [InventoryController::class, 'delete_inventory'])->name('pharmacist.inventory.destroy');
        Route::get('/edit_inventory/{id}',           [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::post('/update_inventory/{id}',        [InventoryController::class, 'update'])->name('inventory.update');

        // Sales
        Route::get('/sales',            [SalesController::class, 'view_sales'])->name('pharmacist.sales');
        Route::get('/add_sale',         [SalesController::class, 'create'])->name('pharmacist.sales.create');
        Route::post('/add_sale',        [SalesController::class, 'add_sale'])->name('pharmacist.sales.store');
        Route::get('/sales/{id}/edit',  [SalesController::class, 'edit'])->name('pharmacist.sales.edit');
        Route::put('/sales/{id}',       [SalesController::class, 'update'])->name('pharmacist.sales.update');

        // Pharmacist claims
        Route::get('/claims',              [InsuranceClaimController::class, 'index'])->name('pharmacist.claims.index');
        Route::get('/claims/create',       [InsuranceClaimController::class, 'create'])->name('pharmacist.claims.create');
        Route::post('/claims',             [InsuranceClaimController::class, 'store'])->name('pharmacist.claims.store');
        Route::get('/claims/{id}',         [InsuranceClaimController::class, 'show'])->name('pharmacist.claims.show');
        Route::post('/claims/{id}/submit', [InsuranceClaimController::class, 'submit'])->name('pharmacist.claims.submit');
    });

    // Admin claims
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/claims',                     [InsuranceClaimController::class, 'admin_index'])->name('claims.index');
        Route::get('/claims/{id}',                [InsuranceClaimController::class, 'show'])->name('claims.show');
        Route::post('/claims/{id}/update-status', [InsuranceClaimController::class, 'update_status'])->name('claims.update_status');
    });

    // ─── Lab Tech Routes ──────────────────────────────────────────
    Route::middleware('role:lab_technician')->group(function () {
        Route::get('/lab',              [LabController::class, 'lab_home'])->name('lab.home');
        Route::get('/lab/queue',        [LabController::class, 'lab_queue'])->name('lab.queue');
        Route::get('/lab/completed',    [LabController::class, 'lab_completed'])->name('lab.completed');
        Route::get('/lab/create',       [LabController::class, 'create_test'])->name('lab.create');
        Route::post('/lab/store',       [LabController::class, 'store_test'])->name('lab.store');
        Route::get('/lab/delete/{id}',  [LabController::class, 'delete_test'])->name('lab.delete');
        Route::get('/lab/upload/{id}',  [LabController::class, 'upload_result'])->name('lab.upload');
        Route::post('/lab/upload/{id}', [LabController::class, 'store_result'])->name('lab.store_result');
        Route::get('/lab/result/{id}',  [LabController::class, 'view_result'])->name('lab.result');
    });

    // ─── Reporting Dashboard Routes ────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports/admin', [ReportController::class, 'admin_dashboard'])->name('reports.admin');
    });
    Route::middleware('role:doctor')->group(function () {
        Route::get('/reports/doctor', [ReportController::class, 'doctor_dashboard'])->name('reports.doctor');
    });
    Route::middleware('role:pharmacist')->group(function () {
        Route::get('/reports/pharmacist', [ReportController::class, 'pharmacist_dashboard'])->name('reports.pharmacist');
    });
    Route::middleware('role:lab_technician')->group(function () {
        Route::get('/reports/lab', [ReportController::class, 'lab_dashboard'])->name('reports.lab');
    });
    Route::middleware('role:patient')->group(function () {
        Route::get('/reports/patient', [ReportController::class, 'patient_dashboard'])->name('reports.patient');
    });

    // ─── Bed Management ─────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/beds/overview',          [BedController::class, 'overview'])->name('beds.overview');
        Route::get('/beds/wards',             [BedController::class, 'wards'])->name('admin.beds.wards');
        Route::get('/beds/wards/create',      [BedController::class, 'create_ward'])->name('admin.beds.create_ward');
        Route::post('/beds/wards/store',      [BedController::class, 'store_ward'])->name('admin.beds.store_ward');
        Route::get('/beds/wards/delete/{id}', [BedController::class, 'delete_ward'])->name('admin.beds.delete_ward');
    });

    Route::middleware('role:admin,doctor,nurse')->group(function () {
        Route::get('/beds',                 [BedController::class, 'beds'])->name('admin.beds.index');
        Route::get('/beds/ward/{ward_id}',  [BedController::class, 'beds'])->name('admin.beds.by_ward');
        Route::post('/beds/status/{id}',    [BedController::class, 'update_bed_status'])->name('admin.beds.status');
        Route::get('/beds/admissions',      [BedController::class, 'admissions'])->name('admin.beds.admissions');
        Route::get('/beds/admit/{bed_id?}', [BedController::class, 'admit_form'])->name('admin.beds.admit');
        Route::post('/beds/admit',          [BedController::class, 'store_admission'])->name('admin.beds.store_admission');
        Route::get('/beds/discharge/{id}',  [BedController::class, 'discharge'])->name('admin.beds.discharge');
        Route::get('/beds/admission/{id}',  [BedController::class, 'admission_detail'])->name('admin.beds.admission_detail');
    });

    // ─── Billing ────────────────────────────────────────────────────
    Route::prefix('billing')->name('billing.')->group(function () {

        // Bills
        Route::get('/',                 [BillingController::class, 'index'])->name('index');
        Route::get('/create',           [BillingController::class, 'create'])->name('create');
        Route::post('/',                [BillingController::class, 'store'])->name('store');
        Route::get('/{bill}',           [BillingController::class, 'show'])->name('show');
        Route::post('/{bill}/open',     [BillingController::class, 'open'])->name('open');
        Route::post('/{bill}/discount', [BillingController::class, 'applyDiscount'])->name('discount');
        Route::post('/{bill}/void',     [BillingController::class, 'void'])->name('void');

        // Bill items
        Route::post('/{bill}/items/service', [BillingController::class, 'addServiceItem'])->name('items.service');
        Route::post('/{bill}/items/manual',  [BillingController::class, 'addManualItem'])->name('items.manual');
        Route::delete('/{bill}/items/{item}', [BillingController::class, 'removeItem'])->name('items.remove');

        // Payments
        Route::post('/{bill}/payments', [BillingController::class, 'recordPayment'])->name('payments.store');
        Route::post('/payments/{payment}/reverse', [BillingController::class, 'reversePayment'])->name('payments.reverse');

        // M-Pesa
        Route::post('/{bill}/mpesa',              [BillingController::class, 'initiateMpesa'])->name('mpesa.initiate');
        Route::get('/mpesa/status/{transaction}', [BillingController::class, 'mpesaStatus'])->name('mpesa.status');

        // Insurance
        Route::post('/{bill}/claims', [BillingController::class, 'createClaim'])->name('claims.store');
    });
});
