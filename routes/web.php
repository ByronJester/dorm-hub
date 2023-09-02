<?php

use App\Http\Controllers\{
    ProfileController, AdminController, OwnerController, TenantController, SharedController, RegisteredUserController
};

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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

    if(Auth::user()) {
        return redirect()->route('login');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => true,
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('landing.page');

// Route::middleware('guest')->group(function () {
//     Route::get('/tenant/register', [RegisteredUserController::class, 'tenantRegister'])->name('tenant.register');
//     Route::get('/owner/register', [RegisteredUserController::class, 'ownerRegister']) ->name('owner.register');
//     Route::post('/user/register', [RegisteredUserController::class, 'store'])->name('user.register');
// });

Route::get('/admin', function () {
    return redirect()->route('admin.login');
});

Route::get('/admin/login', [AdminController::class, 'index'])->name('admin.login');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile-change-password', [ProfileController::class, 'changePassword'])->name('profile.change.password');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['auth', 'cors']], function() {

    Route::prefix('admin')->group(function () {

    });

    Route::prefix('owner')->group(function () {
        Route::get('/dorms', [OwnerController::class, 'dormList'])->name('owner.dorms');
        Route::get('/tenants-application', [OwnerController::class, 'dormList'])->name('owner.tenants.application');
        Route::post('/save-dorm', [OwnerController::class, 'saveDorm'])->name('save.dorm');

    });

    Route::prefix('tenant')->group(function () {
        Route::get('/dorms', [TenantController::class, 'dormList'])->name('tenant.dorms');
    });

    Route::prefix('shared')->group(function () {
        Route::post('/get-long-lat/{address}', [SharedController::class, 'getlongLat'])->name('location.long.lat');
    });
});

require __DIR__.'/auth.php';
