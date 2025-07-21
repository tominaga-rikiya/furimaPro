<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
    /**
     * プロフィール編集画面を表示
     */
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('profile.edit', compact('user', 'profile'));
    }

    /**
     * プロフィール詳細画面を表示
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile;

        if (!$profile) {
            return redirect()->route('profile.edit');
        }

        $activeTab = $request->query('tab', 'listed');

        // 出品した商品
        $listedItems = Item::where('user_id', $user->id)
            ->latest()
            ->get();

        // 購入した商品のデバッグ
        $purchases = Purchase::where('user_id', $user->id)
            ->with('item')
            ->latest()
            ->get();

        // デバッグログ
        \Log::info('User ID: ' . $user->id);
        \Log::info('Purchases count: ' . $purchases->count());

        foreach ($purchases as $purchase) {
            \Log::info('Purchase details:', [
                'purchase_id' => $purchase->id,
                'item_id' => $purchase->item_id,
                'item_exists' => $purchase->item ? 'Yes' : 'No'
            ]);
        }

        // 購入した商品を取得
        $purchasedItems = $purchases->map(function ($purchase) {
            return $purchase->item;
        })->filter();

        return view('profile.profile', compact(
            'user',
            'profile',
            'listedItems',
            'purchasedItems',
            'activeTab'
        ));
    }
    /**
     * プロフィール情報を更新
     */
    public function update(AddressRequest $request)
    {
        $user = auth()->user();

        // プロフィールがなければ新規作成、あれば既存のものを使用
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);

        // プロフィール画像の処理
        if ($request->hasFile('profile_image')) {
            // 既存の画像があれば削除
            if ($profile->profile_image) {
                Storage::delete($profile->profile_image);
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        // プロフィール情報の更新
        $profile->fill([
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        $profile->save();

        // ユーザー名の更新
        $user->update(['name' => $request->name]);

        return redirect()->route('profile.profile')
            ->with('success', 'プロフィールを更新しました');
    }
}
