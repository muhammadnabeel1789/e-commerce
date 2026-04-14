<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| IMPORT CONTROLLERS & MODELS
|--------------------------------------------------------------------------
*/

// --- 1. Controller Public & Customer ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController as PublicProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController as CustomerDashboardController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MidtransCallbackController;

// --- 2. Controller Kurir ---
use App\Http\Controllers\Kurir\KurirDashboardController;
use App\Http\Controllers\Kurir\KurirController;

// --- 3. Controller Admin ---
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\StockLogController as StockLogController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;

// --- 4. Models Laravolt/Indonesia ---
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;

/*
|--------------------------------------------------------------------------
| BAGIAN 1: PUBLIC ROUTES (Bisa diakses tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::controller(PublicProductController::class)->prefix('products')->name('products.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
});

/*
|--------------------------------------------------------------------------
| API WILAYAH LARAVOLT
|--------------------------------------------------------------------------
*/
Route::get('/api/regions/provinces', function () {
    return response()->json(Province::all());
});
Route::get('/api/regions/cities/{province}', function ($province) {
    return response()->json(City::where('province_code', $province)->get());
});
Route::get('/api/regions/districts/{city}', function ($city) {
    return response()->json(District::where('city_code', $city)->get());
});
Route::get('/api/regions/villages/{district}', function ($district) {
    return response()->json(Village::where('district_code', $district)->get());
});

/*
|--------------------------------------------------------------------------
| MIDTRANS WEBHOOK
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/callback', [OrderController::class, 'midtransCallback'])
    ->name('midtrans.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| BAGIAN 2: CUSTOMER ROUTES (Harus Login & Role = customer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Customer
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // 2. Keranjang Belanja
    Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add/{product}', 'addToCart')->name('add');
        Route::patch('/{id}', 'updateCart')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // 3. Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 4. Alamat Pengiriman
    Route::prefix('addresses')->name('addresses.')->controller(UserAddressController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{address}/edit', 'edit')->name('edit');
        Route::put('/{address}', 'update')->name('update');
        Route::delete('/{address}', 'destroy')->name('destroy');
        Route::post('/{address}/set-primary', 'setPrimary')->name('setPrimary');
        Route::post('/{id}/set-default', 'setDefault')->name('setDefault');
    });

    // 5. Checkout
    Route::match(['get', 'post'], '/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/store', [OrderController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/calculate-shipping', [OrderController::class, 'calculateShipping'])->name('checkout.shipping.calculate');
    Route::post('/checkout/cancel-snap', [OrderController::class, 'cancelSnap'])->name('checkout.cancel_snap');
    Route::post('/checkout/update-snap-result', [OrderController::class, 'updateSnapResult'])->name('checkout.update_snap_result');

    // 6. Pembayaran Midtrans
    Route::get('/payment/{orderId}', [OrderController::class, 'paymentPage'])->name('payment.show');

    // 7. Riwayat Pesanan Customer
    Route::prefix('my-orders')->name('customer.orders.')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}/invoice', 'invoice')->name('invoice');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{order}/pay', 'pay')->name('pay');
        Route::post('/{id}/confirm-received', 'confirmReceived')->name('confirm-received');
        Route::post('/{id}/request-cancel', 'requestCancel')->name('request-cancel');
        Route::post('/{id}/mark-paid', 'markPaid')->name('mark-paid');
    });

    // 8. Review / Ulasan Customer
    Route::prefix('reviews')->name('reviews.')->controller(ReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create/{order}/{product}', 'create')->name('create');
        Route::post('/', 'store')->name('store');
    });
});

/*
|--------------------------------------------------------------------------
| BAGIAN 3: KURIR ROUTES (Harus Login & Role = kurir)
| ⚠️ WAJIB di luar group admin — ini group TERPISAH
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:kurir'])
    ->prefix('kurir')
    ->name('kurir.')
    ->group(function () {

        // Dashboard kurir
        Route::get('/dashboard', [KurirDashboardController::class, 'index'])->name('dashboard');

        // Daftar pesanan yang ditugaskan
        Route::get('/orders', [KurirController::class, 'index'])->name('orders.index');

        // Detail pesanan
        Route::get('/orders/{id}', [KurirController::class, 'show'])->name('orders.show');

        // Konfirmasi ambil paket dari toko + upload foto
        Route::post('/orders/{id}/confirm-pickup', [KurirController::class, 'confirmPickUp'])->name('orders.confirm-pickup');

        // Konfirmasi paket sudah diterima customer + upload foto
        Route::post('/orders/{id}/confirm-delivered', [KurirController::class, 'confirmDelivered'])->name('orders.confirm-delivered');
    });

/*
|--------------------------------------------------------------------------
| BAGIAN 4: ADMIN ROUTES (Harus Login & Role = admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // 1. Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 2. Manajemen Produk
    Route::prefix('products')->name('products.')->controller(AdminProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{product}', 'show')->name('show');
        Route::get('/{product}/edit', 'edit')->name('edit');
        Route::put('/{product}', 'update')->name('update');
        Route::delete('/{product}', 'destroy')->name('destroy');
        Route::delete('/image/{id}', 'destroyImage')->name('image.destroy');
        Route::delete('/variant/{id}', 'destroyVariant')->name('variant.destroy');
    });

    // 3. Manajemen Kategori
    Route::prefix('categories')->name('categories.')->controller(AdminCategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{category}/edit', 'edit')->name('edit');
        Route::put('/{category}', 'update')->name('update');
        Route::delete('/{category}', 'destroy')->name('destroy');
    });

    // 4. Manajemen Brand
    Route::prefix('brands')->name('brands.')->controller(AdminBrandController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{brand}/edit', 'edit')->name('edit');
        Route::put('/{brand}', 'update')->name('update');
        Route::delete('/{brand}', 'destroy')->name('destroy');
    });

    // 5. Manajemen User
    Route::prefix('users')->name('users.')->controller(AdminUserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{user}', 'show')->name('show');
        Route::patch('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    // 6. Manajemen Banner
    Route::prefix('banners')->name('banners.')->controller(AdminBannerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{banner}/edit', 'edit')->name('edit');
        Route::put('/{banner}', 'update')->name('update');
        Route::delete('/{banner}', 'destroy')->name('destroy');
    });

    // 7. Manajemen Order (Admin)
    Route::prefix('orders')->name('orders.')->controller(AdminOrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/report', 'report')->name('report');
        Route::get('/{order}', 'show')->name('show');
        Route::put('/{order}', 'update')->name('update');
        Route::post('/{id}/approve-cancel', 'approveCancelRequest')->name('approve-cancel');
        Route::post('/{id}/reject-cancel', 'rejectCancelRequest')->name('reject-cancel');
        // ✅ Assign & unassign kurir (oleh admin)
        Route::post('/{order}/assign-courier', 'assignCourier')->name('assign-courier');
        Route::post('/{order}/unassign-courier', 'unassignCourier')->name('unassign-courier');
        Route::get('/{order}/print-resi', 'printResi')->name('print-resi');
    });

    // 9. Stock Log
    Route::get('/stock-logs', [StockLogController::class, 'index'])->name('stock-logs.index');
    Route::post('/stock-logs', [StockLogController::class, 'store'])->name('stock-logs.store');

    // 9. Manajemen Review
    Route::prefix('reviews')->name('reviews.')->controller(AdminReviewController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/{review}/approve', 'approve')->name('approve');
        Route::delete('/{review}', 'destroy')->name('destroy');
    });

    // Manajemen Setting Toko / Ongkir
    Route::prefix('settings')->name('settings.')->controller(AdminSettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update', 'update')->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';