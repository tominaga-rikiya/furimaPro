<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Message;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function store(MessageRequest $request, SoldItem $soldItem)
    {
        $user = Auth::user();

        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        $data = $request->validated();
        $data['sold_item_id'] = $soldItem->id;
        $data['user_id'] = $user->id;

        if ($request->hasFile('img_url')) {
            $data['image'] = $request->file('img_url')->store('messages', 'public');
        }

        $message = Message::create($data);

        return back()->with('success', 'メッセージを送信しました');
    }

    public function update(MessageRequest $request, Message $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        $data = $request->validated();

        if ($request->hasFile('img_url')) {
            if ($message->image) {
                Storage::disk('public')->delete($message->image);
            }
            $data['image'] = $request->file('img_url')->store('messages', 'public');
        }

        $message->update($data);

        return redirect()->route('transactions.show', $message->soldItem)
            ->with('success', 'メッセージを更新しました');
    }

    public function destroy(Message $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        $soldItem = $message->soldItem;

        if ($message->image) {
            Storage::disk('public')->delete($message->image);
        }

        $message->delete();

        return redirect()->route('transactions.show', $soldItem)
            ->with('success', 'メッセージを削除しました');
    }

    public function markAsRead(Message $message)
    {
        if ($message->user_id !== Auth::id()) {
            $message->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
