<?php

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

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Requests\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', [ItemController::class, 'index'])->name('items.list'); 
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('item.detail');
Route::get('/item', [ItemController::class, 'search']);

Route::middleware(['auth', 'verified'])->group(function () {
    // 商品出品
    Route::get('/sell', [ItemController::class, 'sellView']); 
    Route::post('/sell', [ItemController::class, 'sellCreate']);

    // いいね機能
    Route::post('/item/like/{item_id}', [LikeController::class, 'create']); 
    Route::post('/item/unlike/{item_id}', [LikeController::class, 'destroy']); 

    // コメント機能
    Route::post('/item/comment/{item_id}', [CommentController::class, 'create']); 

    // 購入関連
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index'); 
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->middleware('purchase'); 
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success']); 

    // 配送先住所
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address']); 
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress']); 

    // マイページ
    Route::get('/mypage', [UserController::class, 'mypage']); 
    Route::get('/mypage/profile', [UserController::class, 'profile']); 
    Route::post('/mypage/profile', [UserController::class, 'updateProfile']); 

    // 取引関連
    Route::get('/api/mypage/unread-count', [UserController::class, 'getUnreadCount'])->name('mypage.unread_count'); 
    Route::get('/transactions/{soldItem}', [TransactionController::class, 'showTransaction'])->name('transactions.show'); 
    Route::post('/transactions/{soldItem}/complete', [TransactionController::class, 'complete'])->name('transactions.complete'); 

    // メッセージ機能
    Route::post('/transactions/{soldItem}/messages', [MessageController::class, 'store'])->name('messages.store'); 
    Route::patch('/messages/{message}', [MessageController::class, 'update'])->name('messages.update'); 
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy'); 
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read'); 

    // 評価機能
    Route::post('/transactions/{soldItem}/ratings', [RatingController::class, 'store'])->name('ratings.store'); 
});

// 認証関連
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('email'); 
Route::post('/register', [RegisteredUserController::class, 'store']); 

// メール認証
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice'); 

Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send'); 

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');
    return redirect('/mypage/profile');
})->name('verification.verify'); 