<?php

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

// 商品一覧・詳細・検索
Route::get('/', [ItemController::class, 'index'])->name('items.list'); // トップページ（商品一覧）
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('item.detail'); // 商品詳細ページ
Route::get('/item', [ItemController::class, 'search']); // 商品検索

Route::middleware(['auth', 'verified'])->group(function () {

    // 商品出品関連
    Route::get('/sell', [ItemController::class, 'sellView']); // 出品ページ表示
    Route::post('/sell', [ItemController::class, 'sellCreate']); // 出品処理

    // いいね機能
    Route::post('/item/like/{item_id}', [LikeController::class, 'create']); // いいね追加
    Route::post('/item/unlike/{item_id}', [LikeController::class, 'destroy']); // いいね削除

    // コメント機能
    Route::post('/item/comment/{item_id}', [CommentController::class, 'create']); // コメント投稿

    // 購入関連
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->middleware('purchase')->name('purchase.index'); // 購入確認画面
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->middleware('purchase'); // 購入処理
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success']); // 購入完了画面

    // 配送先住所変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address']); // 住所変更画面
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress']); // 住所更新処理

    // マイページ関連
    Route::get('/mypage', [UserController::class, 'mypage']); // マイページトップ
    Route::get('/mypage/profile', [UserController::class, 'profile']); // プロフィール表示
    Route::post('/mypage/profile', [UserController::class, 'updateProfile']); // プロフィール更新

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

// ログイン処理
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('email');

// 会員登録処理
Route::post('/register', [RegisteredUserController::class, 'store']);

// メール認証通知画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

// 認証メール再送信
Route::post('/email/verification-notification', function (Request $request) {
    session()->get('unauthenticated_user')->sendEmailVerificationNotification();
    session()->put('resent', true);
    return back()->with('message', 'Verification link sent!');
})->name('verification.send');

// メール認証実行
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    session()->forget('unauthenticated_user');
    return redirect('/mypage/profile');
})->name('verification.verify');
