<?php

namespace App\Http\Controllers;

use App\Models\SoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * 取引詳細画面表示
     */
    public function show(SoldItem $soldItem)
    {
        $user = Auth::user();

        // 取引参加者かチェック
        if (!$soldItem->isParticipant($user->id)) {
            abort(403, 'アクセス権限がありません');
        }

        // 商品情報とリレーションを読み込み
        $soldItem->load(['item.user', 'rating.fromUser', 'rating.toUser']);

        // ユーザーの役割を判定
        $userRole = ($soldItem->user_id === $user->id) ? 'buyer' : 'seller';

        return view('transactions.show', compact('soldItem', 'userRole'));
    }

    /**
     * 取引完了（購入者）
     */
    // TransactionController の complete メソッドを以下で置き換え

    public function complete(Request $request, SoldItem $soldItem)
    {
        try {
            // 権限チェック（購入者のみが取引完了できる）
            if ($soldItem->user_id !== auth()->id()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => '権限がありません'
                    ], 403);
                }
                return redirect()->back()->with('error', '権限がありません');
            }

            // 既に完了済みの場合
            if ($soldItem->is_completed) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => '既に取引は完了済みです'
                    ]);
                }
                return redirect()->back()->with('error', '既に取引は完了済みです');
            }

            // 取引完了の処理
            $soldItem->update(['is_completed' => true]);

            // Ajaxリクエストの場合はJSONレスポンスを返す
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '取引が完了しました。評価をお願いします。'
                ]);
            }

            // 通常のリクエストの場合は従来通りリダイレクト
            return redirect()->route('transactions.show', $soldItem)
                ->with('success', '取引が完了しました');
        } catch (\Exception $e) {
            \Log::error('Transaction completion error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => '取引完了処理でエラーが発生しました'
                ], 500);
            }

            return redirect()->back()->with('error', '取引完了処理でエラーが発生しました');
        }
    }

    public function showTransaction(SoldItem $soldItem)
    {
        $user = Auth::user();

        // 取引に関わっているユーザーかチェック
        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $otherUser = $soldItem->user_id === auth()->id()
            ? $soldItem->item->user  // 購入者の場合は出品者
            : $soldItem->user;       // 出品者の場合は購入者


        // 商品情報を取得
        $item = $soldItem->item()->with(['user', 'condition'])->first();

        // メッセージを取得
        $messages = $soldItem->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // 他の取引中の商品を取得
        $otherTransactions = SoldItem::with(['item', 'latestMessage'])
            ->where('id', '!=', $soldItem->id)
            ->where('is_completed', false)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->get()
            ->sortByDesc(function ($transaction) {
                return $transaction->latestMessage ? $transaction->latestMessage->created_at : $transaction->created_at;
            });

        // 相手のメッセージを既読にする
        $soldItem->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

       
        // ユーザーの役割を判定（評価機能用）
        $userRole = ($soldItem->user_id === $user->id) ? 'buyer' : 'seller';

        // 評価情報の読み込み
        try {
            $soldItem->load(['rating.fromUser', 'rating.toUser']);
        } catch (\Exception $e) {
            // 評価情報の読み込みに失敗した場合はログに記録してスキップ
            \Log::warning('Rating relationship load failed: ' . $e->getMessage());
            // 空の評価情報をセット
            $soldItem->setRelation('rating', null);
        }

        return view('chat', compact(
            'soldItem',
            'item',
            'messages',
            'otherTransactions',
            'userRole',
            'otherUser'  
        ));
    }
}
