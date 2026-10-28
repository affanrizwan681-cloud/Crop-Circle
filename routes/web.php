<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\FarmerDashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderEditController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductAdController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WebsiteFeedbackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/{id}', [MarketController::class, 'show'])->name('markets.show');

Route::get('/farmers', [FarmerController::class, 'index'])->name('farmers.index');
Route::get('/farmers/{id}', [FarmerController::class, 'show'])->name('farmers.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/start/{farmerId}', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/{id}/message', [ChatController::class, 'sendMessage'])->name('chat.message');
    Route::get('/chat/{id}/poll', [ChatController::class, 'pollMessages'])->name('chat.poll');


    // Private website feedback from customers and farmers.
    Route::middleware('role:customer,farmer')->group(function () {
        Route::get('/feedback', [WebsiteFeedbackController::class, 'create'])->name('feedback.create');
        Route::post('/feedback', [WebsiteFeedbackController::class, 'store'])->name('feedback.store');
    });
    // Customer Routes
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{id}/edit-proposals', [OrderEditController::class, 'store'])->name('orders.editProposals.store');
        Route::post('/orders/{id}/edit-proposals/{editRequest}/decision', [OrderEditController::class, 'customerDecision'])->name('orders.editProposals.decision');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    });

    // Cart & Checkout (Customer)
    Route::middleware('role:customer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/order/place', [OrderController::class, 'placeOrder'])->name('order.place');

        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });

    // Farmer Routes
    Route::middleware('role:farmer')->prefix('farmer')->name('farmer.')->group(function () {
        Route::get('/pending-approval', [FarmerDashboardController::class, 'pendingApproval'])->name('pending');

        Route::middleware('farmer.approved')->group(function () {
            Route::get('/dashboard', [FarmerDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/ads', [ProductAdController::class, 'farmerIndex'])->name('ads.index');
            Route::post('/ads', [ProductAdController::class, 'store'])->name('ads.store');
            Route::post('/ads/{ad}/removal-request', [ProductAdController::class, 'requestRemoval'])->name('ads.removalRequest');

            // Products
            Route::get('/products', [FarmerDashboardController::class, 'productsIndex'])->name('products.index');
            Route::get('/products/create', [FarmerDashboardController::class, 'createProduct'])->name('products.create');
            Route::post('/products', [FarmerDashboardController::class, 'storeProduct'])->name('products.store');
            Route::get('/products/{id}/edit', [FarmerDashboardController::class, 'editProduct'])->name('products.edit');
            Route::put('/products/{id}', [FarmerDashboardController::class, 'updateProduct'])->name('products.update');
            Route::delete('/products/{id}', [FarmerDashboardController::class, 'deleteProduct'])->name('products.destroy');

            // Weekly Stock
            Route::get('/products/{id}/weekly-stock', [FarmerDashboardController::class, 'manageWeeklyStock'])->name('products.weeklyStock');
            Route::post('/products/{id}/weekly-stock', [FarmerDashboardController::class, 'updateWeeklyStock'])->name('products.weeklyStock.update');

            // Orders
            Route::get('/orders', [FarmerDashboardController::class, 'ordersIndex'])->name('orders.index');
            Route::get('/orders/{id}', [FarmerDashboardController::class, 'orderShow'])->name('orders.show');
            Route::post('/orders/{id}/edit-proposals/{editRequest}/accept', [OrderEditController::class, 'accept'])->name('orders.editProposals.accept');
            Route::post('/orders/{id}/edit-proposals/{editRequest}/deny', [OrderEditController::class, 'deny'])->name('orders.editProposals.deny');
            Route::post('/orders/{id}/status', [FarmerDashboardController::class, 'updateOrderStatus'])->name('orders.status');

            // Pickup Scheduling
            Route::get('/pickups', [FarmerDashboardController::class, 'pickupsIndex'])->name('pickups.index');
            Route::get('/pickup-slots', [FarmerDashboardController::class, 'pickupSlotsIndex'])->name('pickupSlots.index');
            Route::post('/pickup-slots', [FarmerDashboardController::class, 'storePickupSlot'])->name('pickupSlots.store');
            Route::delete('/pickup-slots/{id}', [FarmerDashboardController::class, 'deletePickupSlot'])->name('pickupSlots.destroy');

            // Reviews
            Route::get('/reviews', [FarmerDashboardController::class, 'reviewsIndex'])->name('reviews.index');
            Route::post('/reviews/{id}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');

            // Markets
            Route::get('/markets', [FarmerDashboardController::class, 'marketsIndex'])->name('markets.index');
            Route::post('/markets/apply', [FarmerDashboardController::class, 'applyToMarket'])->name('markets.apply');
        });
    });

    // Administrator Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/ads', [ProductAdController::class, 'adminIndex'])->name('ads.index');
        Route::put('/ads/{ad}', [ProductAdController::class, 'update'])->name('ads.update');

        // Farmers & Approvals
        Route::get('/farmers', [AdminDashboardController::class, 'farmersIndex'])->name('farmers.index');
        Route::post('/farmers/{id}/approve', [AdminDashboardController::class, 'approveFarmer'])->name('farmers.approve');
        Route::post('/farmers/{id}/reject', [AdminDashboardController::class, 'rejectFarmer'])->name('farmers.reject');
        Route::post('/farmers/{id}/toggle-status', [AdminDashboardController::class, 'toggleFarmerStatus'])->name('farmers.toggleStatus');

        // Users
        Route::get('/users', [AdminDashboardController::class, 'usersIndex'])->name('users.index');
        Route::post('/users/{id}/toggle-status', [AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggleStatus');

        // Categories
        Route::get('/categories', [AdminDashboardController::class, 'categoriesIndex'])->name('categories.index');
        Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [AdminDashboardController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminDashboardController::class, 'deleteCategory'])->name('categories.destroy');

        // Markets
        Route::get('/markets', [AdminDashboardController::class, 'marketsIndex'])->name('markets.index');
        Route::post('/markets', [AdminDashboardController::class, 'storeMarket'])->name('markets.store');
        Route::put('/markets/{id}', [AdminDashboardController::class, 'updateMarket'])->name('markets.update');
        Route::post('/market-applications/{farmerProfileId}/{marketId}/approve', [AdminDashboardController::class, 'approveMarketApplication'])->name('marketApplications.approve');
        Route::post('/market-applications/{farmerProfileId}/{marketId}/reject', [AdminDashboardController::class, 'rejectMarketApplication'])->name('marketApplications.reject');

        // Moderation
        Route::get('/products', [AdminDashboardController::class, 'productsIndex'])->name('products.index');
        Route::post('/products/{id}/toggle-moderation', [AdminDashboardController::class, 'toggleProductModeration'])->name('products.toggleModeration');

        Route::get('/feedback', [WebsiteFeedbackController::class, 'adminIndex'])->name('feedback.index');
        Route::put('/feedback/{id}', [WebsiteFeedbackController::class, 'updateStatus'])->name('feedback.updateStatus');
        Route::patch('/feedback/{id}/feature', [WebsiteFeedbackController::class, 'toggleFeature'])->name('feedback.feature');

        Route::get('/reviews', [AdminDashboardController::class, 'reviewsIndex'])->name('reviews.index');
        Route::post('/reviews/{id}/toggle-moderation', [AdminDashboardController::class, 'toggleReviewModeration'])->name('reviews.toggleModeration');
        Route::delete('/reviews/{id}', [AdminDashboardController::class, 'deleteReview'])->name('reviews.destroy');

        Route::get('/reports', [AdminDashboardController::class, 'reportsIndex'])->name('reports.index');
        Route::post('/reports/{id}/resolve', [AdminDashboardController::class, 'resolveReport'])->name('reports.resolve');

        // Orders Oversight
        Route::get('/orders', [AdminDashboardController::class, 'ordersIndex'])->name('orders.index');
        Route::get('/orders/{id}', [AdminDashboardController::class, 'orderShow'])->name('orders.show');

        // Announcements
        Route::get('/announcements', [AdminDashboardController::class, 'announcementsIndex'])->name('announcements.index');
        Route::post('/announcements', [AdminDashboardController::class, 'storeAnnouncement'])->name('announcements.store');
        Route::delete('/announcements/{id}', [AdminDashboardController::class, 'deleteAnnouncement'])->name('announcements.destroy');

        // Audit Activity Logs
        Route::get('/logs', [AdminDashboardController::class, 'activityLogsIndex'])->name('logs.index');
    });
});
