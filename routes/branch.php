<?php

use App\Http\Controllers\Branch\OrderController;
use App\Http\Controllers\Branch\POSController;
use App\Http\Controllers\Branch\SystemController;
use App\Http\Controllers\Branch\BranchCategoryController;
use App\Http\Controllers\Branch\BranchBannerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Branch\NotificationController;
use App\Http\Controllers\Branch\ReportController;
use App\Http\Controllers\Branch\CustomerController;
use App\Http\Controllers\Branch\BranchConversationController;
use App\Http\Controllers\Branch\ReviewsController;
use App\Http\Controllers\Branch\ProductController;


Route::group(['namespace' => 'Branch', 'as' => 'branch.', 'middleware' => 'maintenance_mode'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', 'LoginController@captcha')->name('default-captcha');
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['branch']], function () {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate']);
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');
        Route::post('order-stats', [SystemController::class, 'orderStats'])->name('order-stats');
        Route::get('/get-restaurant-data', [SystemController::class, 'restaurantData'])->name('get-restaurant-data');
        Route::get('dashboard/earning-statistics', [SystemController::class, 'getEarningStatistics'])->name('dashboard.earning-statistics');
        Route::get('ignore-check-order', [SystemController::class, 'ignoreCheckOrder'])->name('ignore-check-order');

        /* POS routes */
        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/', [POSController::class, 'index'])->name('index');
            Route::get('quick-view', [POSController::class, 'quickView'])->name('quick-view');
            Route::post('variant_price', [POSController::class, 'variantPrice'])->name('variant_price');
            Route::post('add-to-cart', [POSController::class, 'addToCart'])->name('add-to-cart');
            Route::post('remove-from-cart', [POSController::class, 'removeFromCart'])->name('remove-from-cart');
            Route::post('cart-items', [POSController::class, 'cartItems'])->name('cart_items');
            Route::post('update-quantity', [POSController::class, 'updateQuantity'])->name('updateQuantity');
            Route::post('empty-cart', [POSController::class, 'emptyCart'])->name('emptyCart');
            Route::post('tax', [POSController::class, 'updateTax'])->name('tax');
            Route::post('discount', [POSController::class, 'updateDiscount'])->name('discount');
            Route::get('customers', [POSController::class, 'getCustomers'])->name('customers');
            Route::post('order', [POSController::class, 'placeOrder'])->name('order');
            Route::get('orders', [POSController::class, 'orderList'])->name('orders');
            Route::get('order-details/{id}', [POSController::class, 'orderDetails'])->name('order-details');
            Route::get('invoice/{id}', [POSController::class, 'generateInvoice']);
            Route::any('store-keys', [POSController::class, 'storeKeys'])->name('store-keys');
            Route::post('customer-store', [POSController::class, 'customerStore'])->name('customer-store');
            Route::get('orders/export', [POSController::class, 'exportOrders'])->name('orders.export');
        });

        /* Orders routes */
        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', [OrderController::class, 'addDeliveryMan'])->name('add-delivery-man');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentRefCode'])->name('add-payment-ref-code');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
        });

        Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
            Route::put('status-update/{id}', 'OrderController@status')->name('status-update');
            Route::get('view/{id}', 'OrderController@view')->name('view');
            Route::post('update-shipping/{id}', 'OrderController@updateShipping')->name('update-shipping');
            Route::delete('delete/{id}', 'OrderController@delete')->name('delete');
            Route::post('search', 'OrderController@search')->name('search');
        });

        /* Branch Category routes */
        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('add', [BranchCategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [BranchCategoryController::class, 'subIndex'])->name('add-sub-category');
            Route::get('add-sub-sub-category', [BranchCategoryController::class, 'subSubIndex'])->name('add-sub-sub-category');
            Route::post('store', [BranchCategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BranchCategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [BranchCategoryController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [BranchCategoryController::class, 'status'])->name('status');
            Route::delete('{id}', [BranchCategoryController::class, 'delete'])->name('delete');
            Route::post('search', [BranchCategoryController::class, 'search'])->name('search');
            Route::get('featured/{id}/{featured}', [BranchCategoryController::class, 'featured'])->name('featured');
        });

        /* Branch Attribute routes */
        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [\App\Http\Controllers\Branch\BranchAttributeController::class, 'index'])->name('add-new');
            Route::post('store', [\App\Http\Controllers\Branch\BranchAttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [\App\Http\Controllers\Branch\BranchAttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [\App\Http\Controllers\Branch\BranchAttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [\App\Http\Controllers\Branch\BranchAttributeController::class, 'delete'])->name('delete');
        });

        Route::prefix('banner')->name('banner.')->group(function () {
            Route::get('list', [BranchBannerController::class, 'index'])->name('list');
            Route::get('create', [BranchBannerController::class, 'create'])->name('create');
            Route::post('store', [BranchBannerController::class, 'store'])->name('store');
            Route::get('{id}/edit', [BranchBannerController::class, 'edit'])->name('edit');
            Route::put('{id}/update', [BranchBannerController::class, 'update'])->name('update');
            Route::get('{id}/status/{status}', [BranchBannerController::class, 'status'])->name('status');
            Route::delete('{id}/delete', [BranchBannerController::class, 'delete'])->name('delete');
        });

        Route::group([
            'prefix' => 'flash-sale',
            'as' => 'flash-sale.'
        ], function () {
            Route::get('/', [\App\Http\Controllers\Branch\FlashSaleController::class, 'index'])->name('index');
            Route::post('store', [\App\Http\Controllers\Branch\FlashSaleController::class, 'store'])->name('store');
            Route::get('edit/{id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'delete'])->name('delete');

            // Flash Sale Product Management
            Route::get('add-product/{flash_sale_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'addProduct'])->name('add-product');
            Route::get('add-product-to-session/{flash_sale_id}/{product_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'addProductToSession'])->name('add-product-to-session');
            Route::get('delete-product-from-session/{flash_sale_id}/{product_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'deleteProductFromSession'])->name('delete-product-from-session');
            Route::get('delete-all-products-from-session/{flash_sale_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'deleteAllProductsFromSession'])->name('delete-all-products-from-session');
            Route::post('add-flash-sale-product/{flash_sale_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'flashSaleProductStore'])->name('add-flash-sale-product');
            Route::delete('product/delete/{flash_sale_id}/{product_id}', [\App\Http\Controllers\Branch\FlashSaleController::class, 'deleteFlashProduct'])->name('product.delete');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::get('add-new', [\App\Http\Controllers\Branch\CouponController::class, 'index'])->name('add-new');
            Route::post('store', [\App\Http\Controllers\Branch\CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [\App\Http\Controllers\Branch\CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [\App\Http\Controllers\Branch\CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [\App\Http\Controllers\Branch\CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [\App\Http\Controllers\Branch\CouponController::class, 'delete'])->name('delete');
            Route::get('details', [\App\Http\Controllers\Branch\CouponController::class, 'details'])->name('details');
        });


        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('add-new', [NotificationController::class, 'index'])->name('add-new');
            Route::post('store', [NotificationController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NotificationController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [NotificationController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [NotificationController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [NotificationController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('order', [ReportController::class, 'orderIndex'])->name('order');
            Route::get('earning', [ReportController::class, 'earningIndex'])->name('earning');
            Route::post('set-date', [ReportController::class, 'setDate'])->name('set-date');
            Route::get('driver-report', [ReportController::class, 'driverReport'])->name('driver-report');
            Route::get('product-report', [ReportController::class, 'productReport'])->name('product-report');
            Route::get('export-product-report', [ReportController::class, 'exportProductReport'])->name('export-product-report');
            Route::get('sale-report', [ReportController::class, 'saleReport'])->name('sale-report');
            Route::get('export-sale-report', [ReportController::class, 'exportSaleReport'])->name('export-sale-report');
            Route::get('wallet-transaction-history', [ReportController::class, 'walletTransactionHistory'])->name('wallet-transaction-history');
            Route::get('export-wallet-transaction-history', [ReportController::class, 'exportWalletTransactionHistory'])->name('export-wallet-transaction-history');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('list', [CustomerController::class, 'customerList'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::get('subscribed-emails', [CustomerController::class, 'subscribedEmails'])->name('subscribed_emails');
            Route::get('export-subscribed-emails', [CustomerController::class, 'exportSubscribedEmails'])->name('export-subscribed-emails');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', [ReviewsController::class, 'list'])->name('list');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', [BranchConversationController::class, 'list'])->name('list');
            Route::post('update-fcm-token', [BranchConversationController::class, 'updateFcmToken'])->name('update_fcm_token');
            Route::get('get-firebase-config', [BranchConversationController::class, 'getFirebaseConfig'])->name('get_firebase_config');
            Route::get('get-conversations', [BranchConversationController::class, 'getConversations'])->name('get_conversations');
            Route::post('store/{user_id}', [BranchConversationController::class, 'store'])->name('store');
            Route::get('view/{user_id}', [BranchConversationController::class, 'view'])->name('view');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::post('search', [ProductController::class, 'search'])->name('search');
            Route::get('bulk-import', [ProductController::class, 'bulkImportIndex'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulkImportProduct']);
            Route::get('bulk-export', [ProductController::class, 'bulkExportProduct'])->name('bulk-export');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('get-categories', [ProductController::class, 'getCategories'])->name('get-categories');
            Route::get('remove-image/{id}/{name}', [ProductController::class, 'removeImage'])->name('remove-image');
        });

    });
});
