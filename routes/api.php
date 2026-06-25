<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/upload', [UploadController::class, 'store']);
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource(
        'categories',
        CategoryController::class
    );

    Route::apiResource(
        'products',
        ProductController::class
    );


    Route::apiResource(
        'variants',
        ProductVariantController::class
    );

    Route::apiResource(
        'customers',
        CustomerController::class
    );

    Route::apiResource(
        'addresses',
        AddressController::class
    );

    Route::apiResource(
        'orders',
        OrderController::class
    );

    Route::get(
        'customers/{customer}/orders',
        [CustomerController::class, 'orders']
    );

    Route::get(
        '/reports/stats',
        [ReportController::class, 'stats']
    );

    Route::get(
        '/reports/dashboard',
        [ReportController::class, 'dashboard']
    );

    Route::get(
        '/reports/top-products',
        [ReportController::class, 'topProducts']
    );

    Route::get(
        '/reports/sales-summary',
        [ReportController::class, 'salesSummary']
    );

    Route::get(
        '/reports/orders-by-status',
        [ReportController::class, 'ordersByStatus']
    );

    Route::patch(
        'orders/{order}/status',
        [OrderController::class, 'updateStatus']
    );

    Route::prefix('cart')->group(function () {

        Route::get('/', [CartController::class, 'show']);

        Route::post('/add', [CartController::class, 'add']);

        Route::post('/update', [CartController::class, 'update']);

        Route::post('/remove', [CartController::class, 'remove']);

        Route::delete('/clear', [CartController::class, 'clear']);

        Route::post(
            '/apply-coupon',
            [CouponController::class, 'apply']
        );
    });

    Route::apiResource(
        'coupons',
        CouponController::class
    );

    Route::get(
        '/riders/{rider}/location',
        [RiderController::class, 'location']
    );

    Route::get(
        '/orders/{order}/track',
        [RiderController::class, 'trackOrder']
    );

    Route::apiResource(
        'riders',
        RiderController::class
    );

    Route::get(
        'orders/{order}/timeline',
        [OrderController::class, 'timeline']
    );

    Route::prefix('rider')->group(function () {

        Route::get('/orders', [RiderController::class, 'orders']);

        Route::post('/location', [RiderController::class, 'updateLocation']);

        Route::post('/status', [RiderController::class, 'updateStatus']);
    });

    Route::patch(
        '/orders/{order}/assign-rider',
        [RiderController::class, 'assignRider']
    );

    Route::get(
        '/notifications/unread-count',
        [NotificationController::class, 'unreadCount']
    );

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllAsRead']
    );

    Route::post(
        '/notifications/{notification}/read',
        [NotificationController::class, 'markAsRead']
    );

    Route::apiResource(
        'notifications',
        NotificationController::class
    )->only([
        'index',
        'show',
        'destroy',
    ]);

    Route::get(
        '/settings',
        [SettingController::class, 'show']
    );

    Route::put(
        '/settings',
        [SettingController::class, 'update']
    );

    Route::patch(
    '/orders/{order}/verify-payment',
    [OrderController::class, 'verifyPayment']
);
});
