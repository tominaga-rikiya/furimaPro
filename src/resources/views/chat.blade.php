@extends('layouts.default')

@section('title', '取引チャット')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/chat.css') }}" >
@endsection

@section('content')
@include('components.header')

<div class="chat-container">
    <div class="chat-layout">
        <aside class="sidebar">
            <h3>その他の取引</h3>
            <div class="transactions-grid">
                @if($otherTransactions->count() > 0)
                    @foreach($otherTransactions as $transaction)
                        <a href="{{ route('transactions.show', $transaction) }}" 
                           class="other-transaction {{ $transaction->id === $soldItem->id ? 'active' : '' }}">
                            <div class="transaction-item">
                                <div class="transaction-image-container">
                                    <img src="{{ \Storage::url($transaction->item->img_url) }}" 
                                         alt="{{ $transaction->item->name }}" 
                                         class="transaction-image">
                                    @if($transaction->unreadMessagesCount(auth()->id()) > 0)
                                        <span class="unread-badge">{{ $transaction->unreadMessagesCount(auth()->id()) }}</span>
                                    @endif
                                </div>
                                <div class="transaction-info">
                                    <div class="transaction-item-name">{{ Str::limit($transaction->item->name, 25) }}</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="no-transactions">
                        <p>他の取引はありません</p>
                    </div>
                @endif
            </div>
        </aside>

        <main class="chat-main">
            <div class="transaction-title-header">
                <div class="transaction-partner-info">
                    <div class="partner-avatar">
                        @if($otherUser->profile_image)
                            <img src="{{ \Storage::url($otherUser->profile_image) }}" alt="{{ $otherUser->name }}のアバター">
                        @else
                            <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="ユーザーアイコン">
                        @endif
                    </div>
                    <h1 class="transaction-title">「{{ $otherUser->name }}」さんとの取引画面</h1>
                </div>
                
                <div class="transaction-complete-action">
                    @if($soldItem->is_completed)
                        @if($soldItem->rating)
                            <div class="already-rated">
                                <span>評価完了</span>
                            </div>
                        @else
                            <button class="rating-button" onclick="openRatingModal()">評価する</button>
                        @endif
                    @else
                        @if($soldItem->user_id === auth()->id())
                            <button type="button" class="complete-transaction-button" onclick="completeTransactionAndShowRating()">
                                取引を完了する
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="product-header">
                <div class="product-info-container">
                    <a href="/item/{{ $item->id }}" class="product-image-link" title="商品詳細を見る">
                        <img src="{{ \Storage::url($item->img_url) }}" alt="{{ $item->name }}">
                    </a>
                    
                    <div class="product-info">
                        <div class="product-title-container">
                            <h2>{{ $item->name }}</h2>
                        </div>
                        <div class="product-price">¥{{ number_format($item->price) }}</div>
                    </div>
                </div>
            </div>

            <div class="messages-area" id="messages-area">
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="message {{ $message->user_id === auth()->id() ? 'own' : 'other' }}" id="message-{{ $message->id }}">
                            
                            <div class="message-container">
                                <div class="message-header">
                                    <div class="message-user-avatar">
                                        @if($message->user->profile && $message->user->profile->img_url)
                                            <img src="{{ \Storage::url($message->user->profile->img_url) }}" alt="{{ $message->user->name }}のアバター">
                                        @else
                                            <div class="default-user-icon"></div>
                                        @endif
                                    </div>
                                    <div class="message-username">{{ $message->user->name }}</div>
                                </div>
                            
                                @php
                                    $isEditing = request('edit') == $message->id;
                                @endphp

                                @if($isEditing && $message->user_id === auth()->id())
                                    <form action="{{ route('messages.update', $message) }}" method="POST" enctype="multipart/form-data" class="edit-message-form">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div class="message-bubble editing">
                                            <div class="form-group">
                                                <textarea 
                                                    name="content" 
                                                    class="edit-textarea" 
                                                    placeholder="メッセージを編集"
                                                    required>{{ old('content', $message->content) }}</textarea>
                                            </div>
                                            
                                            @if($message->image)
                                                <div class="current-image">
                                                    <img src="{{ \Storage::url($message->image) }}" alt="現在の画像" class="message-image">
                                                    <small>現在の画像</small>
                                                </div>
                                            @endif
                                            
                                            <div class="form-group">
                                                <label class="file-label">
                                                    画像を変更
                                                    <input type="file" name="img_url" accept="image/png, image/jpeg" class="file-input">
                                                </label>
                                            </div>
                                            
                                            <div class="edit-actions">
                                                <button type="submit" class="btn-update">更新</button>
                                                <a href="{{ route('transactions.show', $soldItem) }}" class="btn-cancel">キャンセル</a>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="message-bubble">
                                        <div class="message-content">{{ $message->content }}</div>
                                        @if($message->image)
                                            <img src="{{ \Storage::url($message->image) }}" alt="添付画像" class="message-image" onclick="openImageModal(this)">
                                        @endif
                                    </div>
                                    
                                    @if($message->user_id === auth()->id())
                                    <div class="message-actions">
                                        <a href="{{ route('transactions.show', [$soldItem, 'edit' => $message->id]) }}" class="btn-edit">
                                            編集
                                        </a>
                                        <form action="{{ route('messages.destroy', $message) }}" method="POST" style="display: inline;" onsubmit="return confirm('このメッセージを削除しますか？')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
                
                <div class="message-form">
                    <form action="{{ route('messages.store', $soldItem) }}" method="POST" enctype="multipart/form-data" id="message-form">
                        @csrf
                        @error('content')