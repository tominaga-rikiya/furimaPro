<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\User;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Message;
use App\Models\Rating;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        $userRating = $this->getUserRating($user->id);

        return view('profile', compact('profile', 'user', 'userRating'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $img = $request->file('img_url');
        $img_url = isset($img) ? Storage::disk('local')->put('public/img', $img) : '';

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

        User::find(Auth::id())->update(['name' => $request->name]);

        return redirect('/');
    }

    public function mypage(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');

        $items = collect();
        $transactions = collect();

        $unreadData = $this->getUnreadMessageData($user);
        $totalUnreadCount = $unreadData['total_count'];
        $hasNewMessages = $unreadData['has_new'];
        $userRating = $this->getUserRating($user->id);

        if ($page === 'sell') {
            $items = Item::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($page === 'buy') {
            $items = Item::whereHas('soldItem', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('soldItem')->orderBy('created_at', 'desc')->get();
        } elseif ($page === 'transactions') {
            $transactions = $this->getTransactionData($user);
        }

        return view('mypage', compact('user', 'items', 'transactions', 'page', 'totalUnreadCount', 'hasNewMessages', 'userRating'));
    }

    private function getUserRating($userId)
    {
        $ratings = Rating::where('to_user_id', $userId)->get();

        if ($ratings->count() > 0) {
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

    private function getTransactionData($user)
    {
        $transactions = SoldItem::with(['item', 'item.user', 'user', 'messages', 'ratings'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->where(function ($query) use ($user) {
                $query->where('is_completed', false)
                    ->orWhere(function ($subQuery) use ($user) {
                        $subQuery->where('is_completed', true)
                            ->whereDoesntHave('ratings', function ($ratingQuery) use ($user) {
                                $ratingQuery->where('from_user_id', $user->id);
                            });
                    });
            })
            ->get()
            ->map(function ($transaction) use ($user) {
                $isPurchaser = $transaction->user_id === $user->id;
                $partnerUser = $isPurchaser ? $transaction->item->user : $transaction->user;

                $unreadCount = $transaction->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();

                $latestMessage = $transaction->messages()->latest()->first();

                $hasNewMessage = $latestMessage &&
                    $latestMessage->user_id !== $user->id &&
                    $latestMessage->created_at->diffInMinutes(now()) <= 5;

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

    private function getUnreadMessageData($user)
    {
        $totalCount = Message::whereHas('soldItem', function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('item', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    });
            });
        })
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        $hasNew = Message::whereHas('soldItem', function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
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

        $pendingRatingsCount = SoldItem::where('is_completed', true)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('item', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->whereDoesntHave('ratings', function ($query) use ($user) {
                $query->where('from_user_id', $user->id);
            })
            ->count();

        return [
            'total_count' => $totalCount,
            'has_new' => $hasNew,
            'pending_ratings' => $pendingRatingsCount
        ];
    }

    public function showTransaction(SoldItem $soldItem)
    {
        $user = Auth::user();

        if (!$soldItem->isUserInTransaction($user->id)) {
            abort(403, 'この取引にアクセスする権限がありません');
        }

        $soldItem->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $soldItem->messages()->with('user')->orderBy('created_at', 'asc')->get();

        return view('transactions.show', compact('soldItem', 'messages', 'user'));
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $unreadData = $this->getUnreadMessageData($user);

        return response()->json([
            'unread_count' => $unreadData['total_count'],
            'has_new_messages' => $unreadData['has_new'],
            'pending_ratings' => $unreadData['pending_ratings']
        ]);
    }

    public function completeTransaction(SoldItem $soldItem)
    {
        $user = Auth::user();

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
