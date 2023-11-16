<?php

use App\Http\Controllers\{
    ProfileController, AdminController, OwnerController, TenantController, SharedController, RegisteredUserController, AboutUsController, ContactUsController,
    PrivacyPolicyController, TermsUserController, HeroController, HelpController
};

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{ Dorm, TermsUser, Hero};

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
        "terms" => TermsUser::first(),
        "hero" => Hero::first(),
    ]);
})->name('landing.page');

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

Route::get('/alive', function () {
    return response()->json("Keep Alive!", 200);
});

Route::prefix('cron')->group(function () {
    Route::get('/auto-bill', [SharedController::class, 'autoBill']);
    Route::get('/due-reminder', [SharedController::class, 'dueReminder']);
    Route::get('/reservation-expiration', [SharedController::class, 'reservationExpiration']);
});

Route::get('/admin/login', [AdminController::class, 'index'])->name('admin.login');

Route::post('/send-otp', [SharedController::class, 'sendOTP'])->name('send.otp');

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
        //eto doinagdagko
        Route::get('/about-us', [AboutUsController::class, 'show'])->name('aboutUs.show');
        Route::get('/about-us/edit', [AboutUsController::class, 'edit'])->name('aboutUs.edit');
        Route::patch('/about-us/update', [AboutUsController::class, 'update'])->name('aboutUs.update');
        //
        Route::get('/contact-us', [ContactUsController::class, 'show'])->name('contactUs.show');
        Route::get('/contact-us/edit', [ContactUsController::class, 'edit'])->name('contactUs.edit');
        Route::patch('/contact-us/update', [ContactUsController::class, 'update'])->name('contactUs.update');
        //
        Route::get('/policy', [PrivacyPolicyController::class, 'show'])->name('policy.show');
        Route::get('/policy/edit', [PrivacyPolicyController::class, 'edit'])->name('policy.edit');
        Route::patch('/policy/update', [PrivacyPolicyController::class, 'update'])->name('policy.update');
        //
        Route::get('/terms', [TermsUserController::class, 'show'])->name('terms.show');
        Route::get('/terms/edit', [TermsUserController::class, 'edit'])->name('terms.edit');
        Route::patch('/terms/update', [TermsUserController::class, 'update'])->name('terms.update');
        //
        Route::get('/hero/edit', [HeroController::class, 'edit'])->name('hero.edit');
        Route::patch('/hero/update', [HeroController::class, 'update'])->name('hero.update');
        //
        Route::get('/help/edit', [HelpController::class, 'editPage'])->name('help.edit');
        //
        Route::get('/dorms', [AdminController::class, 'dormList'])->name('admin.dorms');
        Route::get('/refund', [AdminController::class, 'refund'])->name('admin.refund');
        Route::get('/tenants', [AdminController::class, 'tenantList'])->name('admin.tenants');
        Route::get('/dashboard', [AdminController:: class, 'dashboard'])->name('admin.dashboard');
        Route::get('/maintenance', [AdminController:: class, 'maintenance'])->name('admin.maintenance');
        Route::get('/reports', [AdminController:: class, 'reports'])->name('admin.reports');
        Route::get('/addUser', [AdminController:: class, 'addUser'])->name('admin.addUser');
        Route::get('/backUp', [AdminController:: class, 'backUp'])->name('admin.backUp');
        Route::get('/archive', [AdminController:: class, 'archive'])->name('admin.archive');
        Route::get('/get-dorm-list', [AdminController::class, 'getDormList'])->name('admin.dorm.list');
        Route::post('/tenant/change-staus', [AdminController::class, 'changeTenantStatus'])->name('tenant.change.status');
        Route::post('/dorm/change-status/{status}', [AdminController::class, 'changeDormStatus'])->name('dorm.change.status');

    });

    Route::prefix('owner')->group(function () {
        Route::get('/dorms', [OwnerController::class, 'dormList'])->name('owner.dorms');
        Route::get('/applicationmodule', [OwnerController::class, 'applications'])->name('owner.manage.application');
        Route::get('/reservation', [OwnerController::class, 'reservation'])->name('owner.manage.reservation');
        Route::get('/tenants', [OwnerController::class, 'tenants'])->name('owner.manage.tenants');
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('owner.dashboard');
        Route::get('/reports', [OwnerController::class, 'reports'])->name('owner.reports');
        Route::get('/addDorm', [OwnerController::class, 'addDorm'])->name('owner.addDorm');
        Route::get('/billings', [OwnerController::class, 'billings'])->name('owner.billing');
        Route::get('/tenantspaymenthistory/{tenant_id}', [OwnerController::class, 'tenanthistory'])->name('owner.tenantshistory');
        Route::get('/request', [OwnerController::class, 'maintenance'])->name('owner.maintenance');
        Route::post('/application/{status}', [OwnerController::class, 'applicationStatusChange'])->name('change.application.status');
        Route::post('/save-dorm', [OwnerController::class, 'saveDorm'])->name('save.dorm');
        Route::post('/payment/mark-as-paid', [OwnerController::class, 'paymentMarkAsPaid'])->name('payment.mark-as-paid');
        Route::post('/application/decline/{id}', [OwnerController::class, 'declineApplication'])->name('decline.application');
        Route::post('/reservation/decline/{id}', [OwnerController::class, 'declineReservation'])->name('decline.reservation');
        Route::post('/application/approve/{id}', [OwnerController::class, 'approveApplication'])->name('approve.application');
        Route::post('/reservation/approve/{id}', [OwnerController::class, 'approveReservation'])->name('approve.reservation');
        Route::post('/payment-history/mark-as-paid', [OwnerController::class, 'paymentHistoryMarkAsPaid'])->name('payment-history.mark-as-paid');
        Route::post('/bill/munual-bill', [OwnerController::class, 'submitManualBill'])->name('owner.manual-bill');
        Route::post('/bill/auto-bill', [OwnerController::class, 'submitAutoBill'])->name('owner.auto-bill');
        Route::post('/room/change-status', [OwnerController::class, 'changeRoomStatus'])->name('change.room.status');
        Route::post('/complain/change-status/{id}', [OwnerController::class, 'changeComplainStatus'])->name('owner.complain.change.status');
        Route::post('/refund/{status}', [OwnerController::class, 'refundChangeStatus'])->name('owner.refund.change.status');
        Route::post('/approve/move-out', [OwnerController::class, 'approveMoveOut'])->name('owner.move.out.tenant');
        Route::post('/tenant/notice-termination', [OwnerController::class, 'noticeTermination'])->name('tenant.notice.termination');
        Route::post('/tenant/remove', [OwnerController::class, 'removeTenant'])->name('tenant.remove');
        Route::post('/tenant/add', [OwnerController::class, 'addTenant'])->name('tenant.add');
        Route::post('/updateDorm', [OwnerController::class, 'updateDorm'])->name('update.dorm');
    });

    Route::prefix('tenant')->group(function () {
        Route::get('/dorms', [TenantController::class, 'dormList'])->name('tenant.dorms');
        Route::get('/payments', [TenantController::class, 'paymentList'])->name('tenant.payments');
        Route::get('/paymongo/success', [TenantController::class, 'successPage']);
        Route::get('/paymongo/failed', [TenantController::class, 'failedPage']);
        Route::get('/mydorm', [TenantController::class, 'mydorm'])->name('tenant.mydorm');
        Route::get('/myreservation', [TenantController::class, 'myreservation'])->name('tenant.reservation');
        Route::get('/message-owner/{owner_id}', [TenantController::class, 'messageOwner'])->name('message.owner');
        Route::get('/billing-info/{param}', [TenantController::class, 'viewBillingInfo'])->name('tenant.billing_info');
        // Route::post('/reserve-room', [TenantController::class, 'reserveRoom'])->name('reserve.room');
        // Route::post('/rent-room', [TenantController::class, 'rentRoom'])->name('rent.room');
        Route::post('/payment/{id}', [TenantController::class, 'payRent'])->name('pay.rent');
        Route::post('/application/apply', [TenantController::class, 'submitApplication'])->name('application.apply');
        Route::post('/reserve/room', [TenantController::class, 'submitRoomReservation'])->name('reserve.room');
        Route::post('/pay/billing', [TenantController::class, 'payBilling'])->name('pay.specific.billing');
        Route::post('/rate/dorm', [TenantController::class, 'rateDorm'])->name('tenant.rate.dorm');
        Route::post('/submit/complain', [TenantController::class, 'submitComplain'])->name('tenant.submit.complain');
        Route::post('/cancel/reservation', [TenantController::class, 'cancelReservation'])->name('cancel.reservation');
        Route::post('/rent/now', [TenantController::class, 'rentNow'])->name('rent.now');
        Route::post('/move-out', [TenantController::class, 'tenantMoveOut'])->name('tenant.move.out');
        Route::post('/request-refund', [TenantController::class, 'requestRefund'])->name('request.refund');
    });

    Route::prefix('shared')->group(function () {
        Route::get('/notification/mark-as-read/{id}', [SharedController::class, 'notificationMarkAsRead'])->name('notification.mark-as-read');
        Route::get('/view/messages', [SharedController::class, 'viewMessages'])->name('view.user.messages');
        Route::get('/fetch/messages', [SharedController::class, 'fetchMessages'])->name('fetch.messages');
        Route::post('/send-message', [SharedController::class, 'sendMessage'])->name('send.message');
        Route::post('/get-long-lat/{address}', [SharedController::class, 'getlongLat'])->name('location.long.lat');
    });
});

Route::get('/aboutUs', [SharedController::class, 'show'])->name('about.us');
Route::get('/policy', [SharedController::class, 'showPolicy'])->name('privacy.policy');
Route::get('/contactUs', [SharedController::class, 'showContact'])->name('contact.us');
Route::get('/view-dorm/{dorm_id}', [SharedController::class, 'viewDorm'])->name('view.dorm');
Route::get('/FAQs', [SharedController::class, 'help'])->name('user.help');

require __DIR__.'/auth.php';
