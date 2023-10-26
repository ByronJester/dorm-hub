<?php

use App\Http\Controllers\{
    ProfileController, AdminController, OwnerController, TenantController, SharedController, RegisteredUserController
};

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{ Dorm };

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

Route::get('/', function () {

    if(Auth::user() && Auth::user()->user_type == 'owner') {
        return redirect()->route('owner.dashboard');
    }

    if(Auth::user() && Auth::user()->user_type == 'tenant') {
        return redirect()->route('tenant.dorms');
    }

    if(Auth::user() && Auth::user()->user_type == 'admin') {
        return redirect()->route('admin.dorms');
    }

    $dorms = Dorm::where('status', 'approved')->get();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => true,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        "dorms" => $dorms,
    ]);
})->name('landing.page');

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

Route::get('/alive', function () {
    return response()->json("Keep Alive!", 200);
});

Route::get('/admin/login', [AdminController::class, 'index'])->name('admin.login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile-change-password', [ProfileController::class, 'changePassword'])->name('profile.change.password');
    Route::get('/profile-payment-settings', [ProfileController::class, 'paymentSettings'])->name('profile.payment-settings');
    Route::post('/profile-income-information', [ProfileController::class, 'updateIncomeInformation'])->name('profile.income_information.update');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['auth', 'cors']], function() {

    Route::prefix('admin')->group(function () {
        Route::get('/dorms', [AdminController::class, 'dormList'])->name('admin.dorms');
        Route::get('/tenants', [AdminController::class, 'tenantList'])->name('admin.tenants');
        Route::get('/get-dorm-list', [AdminController::class, 'getDormList'])->name('admin.dorm.list');
        Route::post('/tenant/change-staus', [AdminController::class, 'changeTenantStatus'])->name('tenant.change.status');
        Route::post('/dorm/change-status/{status}', [AdminController::class, 'changeDormStatus'])->name('dorm.change.status');

    });

    Route::prefix('owner')->group(function () {
        Route::get('/dorms', [OwnerController::class, 'dormList'])->name('owner.dorms');
        Route::get('/applicationmodule', [OwnerController::class, 'applications'])->name('owner.manage.application');
        Route::get('/tenants', [OwnerController::class, 'tenants'])->name('owner.manage.tenants');
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
        Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
        Route::get('/billings', [OwnerController::class, 'billings'])->name('owner.billing');
        Route::get('/maintenance', [OwnerController::class, 'maintenance'])->name('owner.maintenance');
        Route::post('/application/{status}', [OwnerController::class, 'applicationStatusChange'])->name('change.application.status');
        Route::post('/save-dorm', [OwnerController::class, 'saveDorm'])->name('save.dorm');
        Route::post('/payment/mark-as-paid', [OwnerController::class, 'paymentMarkAsPaid'])->name('payment.mark-as-paid');

    });

    Route::prefix('tenant')->group(function () {
        Route::get('/dorms', [TenantController::class, 'dormList'])->name('tenant.dorms');
        Route::get('/payments', [TenantController::class, 'paymentList'])->name('tenant.payments');
        Route::get('/paymongo/success', [TenantController::class, 'successPage']);
        Route::get('/paymongo/failed', [TenantController::class, 'failedPage']);
        Route::get('/mydorm', [TenantController::class, 'mydorm'])->name('tenant.mydorm');
        Route::get('/message-owner/{owner_id}', [TenantController::class, 'messageOwner'])->name('message.owner');
        Route::get('/billing-info/{param}', [TenantController::class, 'viewBillingInfo'])->name('tenant.billing_info');
        // Route::post('/reserve-room', [TenantController::class, 'reserveRoom'])->name('reserve.room');
        // Route::post('/rent-room', [TenantController::class, 'rentRoom'])->name('rent.room');
        Route::post('/payment/{id}', [TenantController::class, 'payRent'])->name('pay.rent');
        Route::post('/application/apply', [TenantController::class, 'submitApplication'])->name('application.apply');
        Route::post('/reserve/room', [TenantController::class, 'submitRoomReservation'])->name('reserve.room');
    });

    Route::prefix('shared')->group(function () {
        Route::get('/notification/mark-as-read/{id}', [SharedController::class, 'notificationMarkAsRead'])->name('notification.mark-as-read');
        Route::get('/view/messages', [SharedController::class, 'viewMessages'])->name('view.user.messages');
        Route::get('/fetch/messages', [SharedController::class, 'fetchMessages'])->name('fetch.messages');
        Route::post('/send-message', [SharedController::class, 'sendMessage'])->name('send.message');
        Route::post('/get-long-lat/{address}', [SharedController::class, 'getlongLat'])->name('location.long.lat');
    });
});

Route::get('/view-dorm/{dorm_id}', [SharedController::class, 'viewDorm'])->name('view.dorm');

require __DIR__.'/auth.php';
