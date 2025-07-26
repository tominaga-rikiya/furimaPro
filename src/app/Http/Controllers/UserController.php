<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Message;
use App\Models\Rating; // ← 追加
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user(); // ← 修正：$userを定義
        $profile = Profile::where('user_id', $user->id)->first();
        $userRating = $this->getUserRating($user->id);

        return view('profile', compact('profile', 'user', 'userRating'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $img = $request->file('img_url');
        if (isset($img)) {
            $img_url = Storage::disk('local')->put('public/img', $img);
        } else {
            $img_url = '';
        }

        $profile = Profile::where('user_id', Auth::id())->first();
        if ($profile) {
            $profile->update([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        } else {
            Profile::create([
                'user_id' => Auth::id(),
                'img_url' => $img_url,
                'postcode' => $request->postcode,
                'address' => $request->address,
                'building' => $request->building
            ]);
        }

        User::find(Auth::id())->update([
            'name' => $request->name
        ]);

        return redirect('/');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $items = collect();
        $transactions = collect();

        // 全ページ共通で未読メッセージ情報を取得
        $unreadData = $this->getUnreadMessageData($user);
        $totalUnreadCount = $unreadData['total_count'];
        $hasNewMessages = $unreadData['has_new'];

        // ユーザーの評価情報を取得（マイページでも表示する場合）
        $userRating = $this->getUserRating($user->id);

        if ($page === 'sell') {
            // 出品した商品
            $items = Item::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($page === 'buy') {
            // 購入した商品
            $items = Item::whereHas('soldItem', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('soldItem')->orderBy('created_at', 'desc')->get();
        } elseif ($page === 'transactions') {
            // 取引中の商品（機能強化版）
            $transactions = $this->getTransactionData($user);
        }

        return view('mypage', compact('user', 'items', 'transactions', 'page', 'totalUnreadCount', 'hasNewMessages', 'userRating'));
    }

    /**
     * ユーザーの評価情報を取得（四捨五入対応）
     * ← 追加：不足していたメソッド
     */
    private function getUserRating($userId)
    {
        $ratings = Rating::where('to_user_id', $userId)->get();

        if ($ratings->count() > 0) {
            // 平均値を計算し、四捨五入する
            $average = round($ratings->avg('score'));
            $total = $ratings->count();
        } else {
            $average = 0;
            $total = 0;
        }

        return [
            'average' => $average,
            'total' => $total,
            'ratings' => $ratings
        ];
    }

    /**
     * 取引データを取得・整形する
     */
    private function getTransactionData($user)
    {
        $transactions = SoldItem::with(['item', 'item.user', 'user', 'messages'])
            ->where('is_completed', false)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->get()
            ->map(function ($transaction) use ($user) {
                // ユーザーの役割を判定
                $isPurchaser = $transaction->user_id === $user->id;
                $partnerUser = $isPurchaser ? $transaction->item->user : $transaction->user;

                // 未読メッセージ数を計算
                $unreadCount = $transaction->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();

                // 最新メッセージを取得
                $latestMessage = $transaction->messages()->latest()->first();

                // 新着メッセージ判定（5分以内）
                $hasNewMessage = $latestMessage &&
                    $latestMessage->user_id !== $user->id &&
                    $latestMessage->created_at->diffInMinutes(now()) <= 5;

                // データを整形
                $transaction->user_role = $isPurchaser ? 'purchaser' : 'seller';
                $transaction->partner_user = $partnerUser;
                $transaction->unread_count = $unreadCount;
                $transaction->latest_message = $latestMessage;
                $transaction->last_message_time = $latestMessage ? $latestMessage->created_at : $transaction->created_at;
                $transaction->has_new_message = $hasNewMessage;

                return $transaction;
            })
            ->sortByDesc('last_message_time')
            ->values();

        return $transactions;
    }

    /**
     * 未読メッセージデータを取得（タブ通知用）
     */
    private function getUnreadMessageData($user)
    {
        // 未読メッセージの合計数を取得
        $totalCount = Message::whereHas('soldItem', function ($query) use ($user) {
            $query->where('is_completed', false)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('item', function ($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        });
                });
        })
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        // 新着メッセージがあるかチェック（5分以内）
        $hasNew = Message::whereHas('soldItem', function ($query) use ($user) {
            $query->where('is_completed', false)
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('item', function ($subQ) use ($user) {
                            $subQ->where('user_id', $user->id);
                        });
                });
        })
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        return [
            'total_count' => $totalCount,
            'has_new' => $hasNew
        ];
    }

    /**
     * 取引の詳細を表示
     */
    public function showTransaction(SoldItem $soldItem)
    {
        $user = Auth::user();

        // アクセス権限チェック
        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません');
        }

        // メッセージを既読にする
        $soldItem->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $soldItem->messages()->with('user')->orderBy('created_at', 'asc')->get();

        return view('transactions.show', compact('soldItem', 'messages', 'user'));
    }

    /**
     * 未読メッセージ数をAJAXで取得（リアルタイム更新用）
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $unreadData = $this->getUnreadMessageData($user);

        return response()->json([
            'unread_count' => $unreadData['total_count'],
            'has_new_messages' => $unreadData['has_new']
        ]);
    }

    /**
     * 取引完了処理
     */
    public function completeTransaction(SoldItem $soldItem)
    {
        $user = Auth::user();

        // アクセス権限チェック
        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません');
        }

        $soldItem->update([
            'is_completed' => true,
            'completed_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}
