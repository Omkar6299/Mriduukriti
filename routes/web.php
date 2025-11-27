<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\NTTDataPaymentController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\CustomerLoginController;
use App\Http\Controllers\Frontend\WhistlistController;
// use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landingPage'])->name('landingPage');

Route::get('/collections/all', [\App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('frontend.shop.list');

Route::get('/collections/{product_slug}', [\App\Http\Controllers\Frontend\ProductController::class, 'productPage'])->name('frontend.product.page');

Route::get('/about', function () {
    return view('frontend.about.index');
})->name('frontend.about.index');

Route::get('/FAQs', function () {
    return view('frontend.Faqs.index');
})->name('frontend.faqs.index');

Route::get('/contact', function () {
    return view('frontend.contact.index');
})->name('frontend.contact.index');

Route::prefix('p')->group(function () {
    Route::get('/terms-conditions', function () {
        return view('frontend.other_pages.term_condition');
    })->name('frontend.other_pages.term_condition');
    Route::get('/shipping-policy', function () {
        return view('frontend.other_pages.shipping');
    })->name('frontend.other_pages.shipping');
});
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{productId}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/remove/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/update-quantity/{cartItemId}', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
});
Route::prefix('whistlist/')->group(function () {
    //  Route::get('/', [WhistlistController::class, 'index'])->name('cart.index');
    Route::post('/toggle/{productId}', [WhistlistController::class, 'toggleWishlist'])->name('wishlist.toggle');
});


// Subcriber route

Route::resource('new_letter_subscribe', NewsLetterController::class);

Route::post('/customer/login', [CustomerLoginController::class, 'customerLogin'])->name('customer.loginPost');

Route::get('auth/google', [CustomerLoginController::class, 'redirect'])->name('google.login');
Route::get('auth/google/callback', [CustomerLoginController::class, 'callback'])->name('googleCallbackUrl');

// NTT Data Payment Gateway Routes (Public callback and return - no auth required)
Route::post('/payment/nttdata/callback', [NTTDataPaymentController::class, 'paymentCallback'])
    ->name('nttdata.payment.callback');
Route::match(['get', 'post'], '/customer/payment/nttdata/return/{orderId}', [NTTDataPaymentController::class, 'paymentReturn'])
    ->name('nttdata.payment.return');

// Customer web routes start here
Route::middleware(['customer'])
    ->prefix('customer')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('frontend.users.profile.index');
        })->name('user_dashboard');
        Route::post('/order-processing', [OrderController::class, 'orderStore'])->name('customer.orderStore');
        Route::get('/order-confirmed/{id}', [OrderController::class, 'orderPageCod'])->name('customer.orderPageCod');
        Route::put('/orders/{id}/cancel', [OrdersController::class, 'ordercancel'])->name('customer.orderCancel');
        Route::post('/customer/logout', [CustomerLoginController::class, 'customerLogout'])->name('customer.logout');

        // NTT Data Payment Routes
        Route::get('/payment/nttdata/initiate/{orderId}', [NTTDataPaymentController::class, 'initiatePayment'])
            ->name('nttdata.payment.initiate');
    });

// Customer web routes end here

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Route Here
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Category Routes Here
    Route::resource('/category', CategoryController::class);
    Route::resource('/sub_category', SubCategoryController::class);
    Route::resource('/attribute', AttributeController::class);
    Route::resource('/products', ProductController::class);
    Route::resource('/banners', BannerController::class);
    Route::resource('/setting', SettingController::class);
    Route::resource('/users', UserController::class);
    Route::resource('/subscribers', SubscriberController::class);

    Route::get('/payment-transactions', [PaymentTransactionController::class, 'index'])->name('payment-transactions.index');
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order_no}', [OrdersController::class, 'orderDetail'])->name('orders.detail');
    Route::put('/orders/{order}/status', [OrdersController::class, 'orderUpdateStatus'])->name('orders.orderUpdateStatus');

    Route::get('/getSucategoryByCategory', [ProductController::class, 'getSucategoryByCategory'])->name('getSucategoryByCategory');

    // Profile Route Here
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
