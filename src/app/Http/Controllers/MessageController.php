<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Message;
use App\Models\SoldItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * メッセージ送信
     */
    public function store(MessageRequest $request, SoldItem $soldItem)
    {
        $user = Auth::user();

        // 取引に関わっているかチェック
        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $data = $request->validated();
        $data['sold_item_id'] = $soldItem->id;
        $data['user_id'] = $user->id;

        // 画像アップロード処理
        if ($request->hasFile('img_url')) {
            $data['image'] = $request->file('img_url')->store('messages', 'public');
        }

        $message = Message::create($data);

        return back()->with('success', 'メッセージを送信しました');
    }

    /**
     * メッセージ編集
     */
    public function update(MessageRequest $request, Message $message)
    {
        // 自分のメッセージかチェック
        if ($message->user_id !== Auth::id()) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        $data = $request->validated();

        // 画像アップロード処理
        if ($request->hasFile('img_url')) {
            // 既存画像を削除
            if ($message->image) {
                Storage::disk('public')->delete($message->image);
            }
            $data['image'] = $request->file('img_url')->store('messages', 'public');
        }

        $message->update($data);

        return redirect()->route('transactions.show', $message->soldItem)
            ->with('success', 'メッセージを更新しました');
    }

    /**
     * メッセージ削除
     */
    public function destroy(Message $message)
    {
        // 自分のメッセージかチェック
        if ($message->user_id !== Auth::id()) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        $soldItem = $message->soldItem;

        // 画像ファイルも削除
        if ($message->image) {
            Storage::disk('public')->delete($message->image);
        }

        $message->delete();

        return redirect()->route('transactions.show', $soldItem)
            ->with('success', 'メッセージを削除しました');
    }

    /**
     * メッセージを既読にする
     */
    public function markAsRead(Message $message)
    {
        // 自分以外のメッセージを既読にする
        if ($message->user_id !== Auth::id()) {
            $message->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
