<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\SoldItem;
use App\Http\Requests\RatingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function store(RatingRequest $request, SoldItem $soldItem)
    {
        $user = Auth::user();

        $isPurchaser = $soldItem->user_id === $user->id;
        $isSeller = $soldItem->item->user_id === $user->id;

        if (!$isPurchaser && !$isSeller) {
            return response()->json(['success' => false, 'message' => 'アクセス権限がありません'], 403);
        }

        if (!$soldItem->is_completed) {
            return response()->json(['success' => false, 'message' => '取引が完了していません'], 400);
        }

        if ($soldItem->hasUserRated($user->id)) {
            return response()->json(['success' => false, 'message' => '既に評価済みです'], 400);
        }

        $toUserId = $isPurchaser ? $soldItem->item->user_id : $soldItem->user_id;

        try {
            DB::beginTransaction();

            Rating::create([
                'sold_item_id' => $soldItem->id,
                'from_user_id' => $user->id,
                'to_user_id' => $toUserId,
                'score' => $request->score,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '評価を送信しました',
                'redirect_url' => route('items.list')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => '評価の送信に失敗しました: ' . $e->getMessage()], 500);
        }
    }
}
