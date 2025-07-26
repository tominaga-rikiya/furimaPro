<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;
use App\Models\SoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function show(SoldItem $soldItem)
    {
        if (!$soldItem->isParticipant(Auth::id())) {
            abort(403, 'アクセス権限がありません');
        }

        $otherUser = $soldItem->user_id === Auth::id()
            ? $soldItem->item->user
            : $soldItem->user;

        $item = $soldItem->item()->with(['user', 'condition'])->first();

        $messages = $soldItem->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $otherTransactions = SoldItem::with(['item', 'latestMessage'])
            ->where('id', '!=', $soldItem->id)
            ->where('is_completed', false)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('item', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->get()
            ->sortByDesc(function ($transaction) {
                return $transaction->latestMessage ? $transaction->latestMessage->created_at : $transaction->created_at;
            });

        $soldItem->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $userRole = ($soldItem->user_id === Auth::id()) ? 'buyer' : 'seller';

        return view('chat', compact(
            'soldItem',
            'item',
            'messages',
            'otherTransactions',
            'userRole',
            'otherUser'
        ));
    }

    public function complete(Request $request, SoldItem $soldItem)
    {
        try {
            if ($soldItem->user_id !== Auth::id()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => '権限がありません'
                    ], 403);
                }
                return redirect()->back()->with('error', '権限がありません');
            }

            if ($soldItem->is_completed) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => '既に取引は完了済みです'
                    ]);
                }
                return redirect()->back()->with('error', '既に取引は完了済みです');
            }

            DB::beginTransaction();

            $soldItem->update([
                'is_completed' => true,
                'completed_at' => now()
            ]);

            $this->sendTransactionCompletedMail($soldItem);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '取引が完了しました。評価をお願いします。'
                ]);
            }

            return redirect()->route('transactions.show', $soldItem)
                ->with('success', '取引が完了しました');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction completion error: ' . $e->getMessage());

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
        if (!$soldItem->isUserInTransaction(Auth::id())) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $otherUser = $soldItem->user_id === Auth::id()
            ? $soldItem->item->user
            : $soldItem->user;

        $item = $soldItem->item()->with(['user', 'condition'])->first();

        $messages = $soldItem->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $otherTransactions = SoldItem::with(['item', 'latestMessage'])
            ->where('id', '!=', $soldItem->id)
            ->where('is_completed', false)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('item', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->get()
            ->sortByDesc(function ($transaction) {
                return $transaction->latestMessage ? $transaction->latestMessage->created_at : $transaction->created_at;
            });

        $soldItem->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $userRole = ($soldItem->user_id === Auth::id()) ? 'buyer' : 'seller';

        try {
            $soldItem->load(['rating.fromUser', 'rating.toUser']);
        } catch (\Exception $e) {
            Log::warning('Rating relationship load failed: ' . $e->getMessage());
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

    private function sendTransactionCompletedMail(SoldItem $soldItem)
    {
        try {
            $soldItem->load(['item.user']);
            $buyer = Auth::user();

            if (!$soldItem->item) {
                throw new \Exception('商品データが見つかりません');
            }

            if (!$soldItem->item->user) {
                throw new \Exception('販売者データが見つかりません');
            }

            if (!$soldItem->item->user->email) {
                throw new \Exception('販売者のメールアドレスが設定されていません');
            }

            Log::info('取引完了メール送信開始', [
                'sold_item_id' => $soldItem->id,
                'to_email' => $soldItem->item->user->email,
                'seller_name' => $soldItem->item->user->name,
                'buyer_name' => $buyer->name,
                'item_name' => $soldItem->item->name
            ]);

            Mail::to($soldItem->item->user->email)
                ->send(new TransactionCompletedMail($soldItem, $buyer));

            Log::info('取引完了メール送信完了');
        } catch (\Exception $e) {
            Log::error('取引完了メール送信失敗', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'sold_item_id' => $soldItem->id ?? 'unknown'
            ]);
        }
    }
}
