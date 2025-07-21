<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

// 認証関連（ログイン・登録・ログアウト）
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 新規登録関連
Route::get('/register', function () {
    return view('auth.register');
});
Route::post('/register', [AuthController::class, 'store'])->name('register');

// メール認証関連
Route::controller(AuthController::class)->group(function () {
    Route::get('/email/verify', 'showVerifyNotice')
        ->name('verification.notice');
    Route::post('/email/verification-notification', 'resendVerificationEmail')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', 'verifyEmail')
        ->middleware(['throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verify-redirect', 'verifyRedirect')
        ->name('verification.verify-redirect');
    Route::get('/check-email-verification', 'checkEmailVerification')
        ->middleware(['auth'])
        ->name('verification.check');
});


Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/items', [ItemController::class, 'index'])->name('item.index');
Route::get('/items/{tab}', [ItemController::class, 'index']);


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile', [ProfileController::class, 'store'])->name('profile.store');
});

Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::prefix('item/{item_id}')->group(function () {
    Route::post('/favorite', [ItemController::class, 'favorite'])->name('item.favorite');
    Route::delete('/favorite', [ItemController::class, 'unfavorite'])->name('item.unfavorite');
    Route::post('/comment', [ItemController::class, 'comment'])->name('item.comment');
});


Route::prefix('item/{item_id}')->group(function () {
    Route::post('/favorite', [ItemController::class, 'favorite'])->name('item.favorite');
    Route::delete('/favorite', [ItemController::class, 'toggleFavorite']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

    Route::get('purchase/address/{item_id}', [PurchaseController::class, 'showAddressForm'])->name('purchase.address');
    Route::put('purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.update.address');
    }
);









// Route::get('/', [ItemController::class, 'index'])->name('items.list');
// Route::get('/item/{item}', [ItemController::class, 'detail'])->name('item.detail');
// Route::get('/item', [ItemController::class, 'search']);

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/sell', [ItemController::class, 'sellView']);
//     Route::post('/sell', [ItemController::class, 'sellCreate']);
//     Route::post('/item/like/{item_id}', [LikeController::class, 'create']);
//     Route::post('/item/unlike/{item_id}', [LikeController::class, 'destroy']);
//     Route::post('/item/comment/{item_id}', [CommentController::class, 'create']);
//     Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index');
//     Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->middleware('purchase');
//     Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success']);
//     Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address']);
//     Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress']);
//     Route::get('/mypage', [UserController::class, 'mypage']);
//     Route::get('/mypage/profile', [UserController::class, 'profile']);
//     Route::post('/mypage/profile', [UserController::class, 'updateProfile']);
// });

