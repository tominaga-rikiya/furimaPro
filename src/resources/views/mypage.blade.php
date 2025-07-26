@extends('layouts.default')

@section('title','マイページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css') }}">
<link rel="stylesheet" href="{{ asset('/css/mypage.css') }}">
@endsection

@section('content')

@include('components.header')

<div class="container">
    <div class="user">
        <div class="user__info">
            <div class="user__img">
                @if (isset($user->profile->img_url))
                    <img class="user__icon" src="{{ \Storage::url($user->profile->img_url) }}" alt="">
                @else
                    <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="ユーザーアイコン">
                @endif
            </div>
            <div class="user__details">
                <p class="user__name">{{$user->name}}</p>
                @if(isset($userRating) && $userRating['total'] > 0)
                    <div class="profile-rating">
                        <div class="rating-stars-display">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $userRating['average'] ? 'filled' : 'empty' }}">★</span>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mypage__user--btn">
            <a class="btn2" href="/mypage/profile">プロフィールを編集</a>
        </div>
    </div>
    
    <div class="border">
        <ul class="border__list">
            <li>
                <a href="/mypage?page=sell" class="{{ $page === 'sell' ? 'active' : '' }}">
                    出品した商品
                </a>
            </li>
            <li>
                <a href="/mypage?page=buy" class="{{ $page === 'buy' ? 'active' : '' }}">
                    購入した商品
                </a>
            </li>
            <li>
                <a href="/mypage?page=transactions" class="{{ $page === 'transactions' ? 'active' : '' }}">
                    取引中の商品
                    @if(isset($totalUnreadCount) && $totalUnreadCount > 0)
                        <span class="tab-notification {{ $totalUnreadCount > 99 ? 'large-count' : '' }} {{ isset($hasNewMessages) && $hasNewMessages ? 'has-new' : '' }}">
                            {{ $totalUnreadCount > 999 ? '999+' : $totalUnreadCount }}
                        </span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    @if($page !== 'transactions')
        <div class="items">
            @foreach ($items as $item)
                <div class="item">
                    <a href="/item/{{$item->id}}">
                        @if ($item->sold())
                            <div class="item__img--container sold">
                                <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                            </div>
                        @else
                            <div class="item__img--container">
                                <img src="{{ \Storage::url($item->img_url) }}" class="item__img" alt="商品画像">
                            </div>
                        @endif
                        <p class="item__name">{{$item->name}}</p>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($page === 'transactions')
        <div class="items">
            @if($transactions->count() > 0)
                @foreach($transactions as $transaction)
                    <div class="item">
                        <a href="{{ route('transactions.show', $transaction) }}">
                            <div class="item__img--container">
                                <img src="{{ \Storage::url($transaction->item->img_url) }}" class="item__img" alt="商品画像">
                                @if($transaction->unread_count > 0)
                                    <span class="unread-badge {{ $transaction->unread_count >= 10 ? 'double-digit' : '' }} {{ $transaction->has_new_message ? 'new-message' : '' }}">
                                        {{ $transaction->unread_count > 99 ? '99+' : $transaction->unread_count }}
                                    </span>
                                @endif
                            </div>
                            <p class="item__name">{{ $transaction->item->name }}</p>
                        </a>
                    </div>
                @endforeach             
            @endif
        </div>
    @endif
</div>
@endsection