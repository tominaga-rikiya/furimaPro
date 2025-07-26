@extends('layouts.default')

@section('title', '取引チャット')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/chat.css') }}" >
@endsection

@section('content')
@include('components.header')

<div class="chat-container">
    <div class="chat-layout">
        <!-- 上部：その他の取引（横並び） -->
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

        <!-- メインチャットエリア -->
        <main class="chat-main">
            <!-- 取引タイトルヘッダー（ボタンを右横に配置） -->
            <div class="transaction-title-header">
                <div class="transaction-partner-info">
                    <div class="partner-avatar">
                        <!-- ユーザーのアバター画像があれば表示、なければデフォルト -->
                        @if($otherUser->profile_image)
                            <img src="{{ \Storage::url($otherUser->profile_image) }}" alt="{{ $otherUser->name }}のアバター">
                        @else
                            <img id="myImage" class="user__icon" src="{{ asset('img/icon.png') }}" alt="ユーザーアイコン">
                        @endif
                    </div>
                    <h1 class="transaction-title">「{{ $otherUser->name }}」さんとの取引画面</h1>
                </div>
                
                <!-- 取引ステータスボタンを右側に配置 -->
                <div class="transaction-complete-action">
                    @if($soldItem->is_completed)
                        @if($soldItem->rating)
                            <!-- 既に評価済みの場合 -->
                            <div class="already-rated">
                                <span>評価完了</span>
                            </div>
                        @else
                            <!-- 評価待ちの場合 -->
                            <button class="rating-button" onclick="openRatingModal()">評価する</button>
                        @endif
                    @else
                        <!-- 取引中の場合：取引完了ボタンを表示 -->
                        @if($soldItem->user_id === auth()->id())
                            <!-- 購入者のみが取引完了ボタンを押せる -->
                            <button type="button" class="complete-transaction-button" onclick="completeTransactionAndShowRating()">
                                取引を完了する
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <!-- 商品情報ヘッダー -->
            <div class="product-header">
                <div class="product-info-container">
                    <!-- 商品画像（クリックで商品詳細へ） -->
                    <a href="/item/{{ $item->id }}" class="product-image-link" title="商品詳細を見る">
                        <img src="{{ \Storage::url($item->img_url) }}" alt="{{ $item->name }}">
                    </a>
                    
                    <!-- 商品情報 -->
                    <div class="product-info">
                        <div class="product-title-container">
                            <h2>{{ $item->name }}</h2>
                        </div>
                        <div class="product-price">¥{{ number_format($item->price) }}</div>
                    </div>
                </div>
            </div>

            <!-- メッセージ表示エリア -->
            <div class="messages-area" id="messages-area">
                @if($messages->count() > 0)
                    @foreach($messages as $message)
                        <div class="message {{ $message->user_id === auth()->id() ? 'own' : 'other' }}" id="message-{{ $message->id }}">
                            
                            <div class="message-container">
                                <!-- メッセージヘッダー（アイコン・ユーザー名） -->
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
                                    <!-- 編集モード -->
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
                                    <!-- 通常表示モード -->
                                    <div class="message-bubble">
                                        <div class="message-content">{{ $message->content }}</div>
                                        @if($message->image)
                                            <img src="{{ \Storage::url($message->image) }}" alt="添付画像" class="message-image" onclick="openImageModal(this)">
                                        @endif
                                    </div>
                                    
                                    <!-- 自分のメッセージの場合、編集・削除ボタンを表示（メッセージボックスの外） -->
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
                
                <!-- FN006: メッセージ投稿フォーム -->
                <div class="message-form">
                    <form action="{{ route('messages.store', $soldItem) }}" method="POST" enctype="multipart/form-data" id="message-form">
                        @csrf
                        @error('content')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                            @error('img_url')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        
                        <div class="form-group">
                            <input 
                                type="text"
                                name="content" 
                                id="content" 
                                class="form-control" 
                                placeholder="取引メッセージを記入してください"
                                value="{{ old('content') }}"
                            >

                            <label class="btn2" id="file-btn">
                                画像を追加
                                <input id="target" class="btn2--input" type="file" name="img_url" accept="image/*">
                            </label>
                            
                            <div class="selected-file-info" id="selected-file-info"></div>

                               <button type="submit" class="btn-send" id="send-btn"> <img src="{{ asset('img/send.png') }}" alt="送信アイコン" style="height:5%;"></button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- 評価モーダル -->
<div id="rating-modal" class="rating-modal" style="display: none;">
    <div class="rating-modal-content">
        <button class="rating-modal-close" onclick="closeRatingModal()">&times;</button>
        
        <div class="rating-modal-header">
            <h2 class="rating-modal-title">取引が完了しました。</h2>
            
        </div>
        
        <div class="rating-divider">
            <p class="rating-modal-subtitle">今回の取引相手はどうでしたか？</p>
        </div>

        <form id="rating-form">
            @csrf
            
            <!-- 星評価 -->
            <div class="rating-form-group">
                <div class="rating-stars" id="rating-stars">
                    <span class="rating-star" data-rating="1">★</span>
                    <span class="rating-star" data-rating="2">★</span>
                    <span class="rating-star" data-rating="3">★</span>
                    <span class="rating-star" data-rating="4">★</span>
                    <span class="rating-star" data-rating="5">★</span>
                </div>
                <div class="rating-error" id="score-error" style="display: none;"></div>
            </div>

            <div class="rating-divider"></div>

            <div class="rating-actions">
                <button type="submit" class="rating-btn-submit" id="rating-submit-btn" disabled>送信する</button>
            </div>
        </form>
    </div>
</div>

<!-- 画像モーダル（初期状態で非表示） -->
<div id="image-modal" class="modal" style="display: none;" onclick="closeImageModal()">
    <div class="modal-content">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img id="modal-image" src="" alt="拡大画像">
    </div>
</div>

@endsection

<script>
    let selectedRating = 0;

    // 取引完了 + 評価モーダル表示（評価送信まで完了しない）
    function completeTransactionAndShowRating() {
        // 評価モーダルを表示（取引完了処理は評価送信時に行う）
        openRatingModal();
        
        // ボタンを一時的に無効化
        const completeBtn = document.querySelector('.complete-transaction-button');
        if (completeBtn) {
            completeBtn.disabled = true;
            completeBtn.textContent = '評価待ち...';
        }
    }

    // 評価モーダル関連
    function openRatingModal() {
        document.getElementById('rating-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeRatingModal() {
        document.getElementById('rating-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
        resetRatingForm();
        
        // 取引完了ボタンを元に戻す
        const completeBtn = document.querySelector('.complete-transaction-button');
        if (completeBtn) {
            completeBtn.disabled = false;
            completeBtn.textContent = '取引を完了する';
        }
    }

    function closeRatingModalForce() {
        document.getElementById('rating-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
        resetRatingForm();
    }

    function resetRatingForm() {
        selectedRating = 0;
        document.querySelectorAll('.rating-star').forEach(star => {
            star.classList.remove('active');
        });
        document.getElementById('rating-submit-btn').disabled = true;
        
        // エラーメッセージをクリア
        document.querySelectorAll('.rating-error').forEach(error => {
            error.style.display = 'none';
        });
    }

    function highlightStars(rating) {
        document.querySelectorAll('.rating-star').forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffd700';
            } else {
                star.style.color = '#ddd';
            }
        });
    }

    function updateStars() {
        document.querySelectorAll('.rating-star').forEach((star, index) => {
            if (index < selectedRating) {
                star.classList.add('active');
                star.style.color = '#ffd700';
            } else {
                star.classList.remove('active');
                star.style.color = '#ddd';
            }
        });
    }

    // 画像モーダル
    function openImageModal(img) {
        document.getElementById('modal-image').src = img.src;
        document.getElementById('image-modal').style.display = 'block';
    }

    function closeImageModal() {
        document.getElementById('image-modal').style.display = 'none';
    }

    // 統合されたDOMContentLoaded
    document.addEventListener('DOMContentLoaded', function() {
        
        // 編集ボタンクリック時のスクロール位置保存
        document.querySelectorAll('.btn-edit').forEach(function(editBtn) {
            editBtn.addEventListener('click', function(e) {
                sessionStorage.setItem('chatScrollPosition', document.querySelector('.messages-area').scrollTop);
            });
        });
        
        // ページ読み込み時にスクロール位置を復元
        const messagesArea = document.querySelector('.messages-area');
        if (messagesArea) {
            const savedScrollPosition = sessionStorage.getItem('chatScrollPosition');
            if (savedScrollPosition) {
                setTimeout(function() {
                    messagesArea.scrollTop = parseInt(savedScrollPosition);
                    sessionStorage.removeItem('chatScrollPosition');
                }, 100);
            }
        }
        
        // URLに編集パラメータがある場合、該当メッセージまでスクロール
        const urlParams = new URLSearchParams(window.location.search);
        const editingMessageId = urlParams.get('edit');
        if (editingMessageId && messagesArea) {
            setTimeout(function() {
                const editingMessage = document.getElementById('message-' + editingMessageId);
                if (editingMessage) {
                    editingMessage.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }, 200);
        }
        
        // ファイル選択の視覚的フィードバック
        const fileInput = document.getElementById('target');
        const fileBtn = document.getElementById('file-btn');
        const selectedFileInfo = document.getElementById('selected-file-info');
        
        if (fileInput && fileBtn) {
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const fileSize = Math.round(this.files[0].size / 1024);
                    
                    fileBtn.classList.add('file-selected');
                    selectedFileInfo.textContent = `選択済み: ${fileName} (${fileSize}KB)`;
                    selectedFileInfo.classList.add('show');
                } else {
                    fileBtn.classList.remove('file-selected');
                    selectedFileInfo.classList.remove('show');
                }
            });
        }
        
        // メッセージフォームの処理
        const messageForm = document.getElementById('message-form');
        const messageInput = document.querySelector('input[name="content"]');
        
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                setTimeout(function() {
                    if (messageInput) messageInput.value = '';
                    if (fileInput) {
                        fileInput.value = '';
                        fileBtn.classList.remove('file-selected');
                        selectedFileInfo.classList.remove('show');
                    }
                    
                    const transactionId = window.location.pathname.split('/').pop();
                    const storageKey = `transaction_message_${transactionId}`;
                    sessionStorage.removeItem(storageKey);
                }, 100);
            });
        }
        
        // 入力保持機能
        if (messageInput) {
            const transactionId = window.location.pathname.split('/').pop();
            const storageKey = `transaction_message_${transactionId}`;
            
            const savedMessage = sessionStorage.getItem(storageKey);
            if (savedMessage) {
                messageInput.value = savedMessage;
            }
            
            messageInput.addEventListener('input', function() {
                const currentValue = this.value.trim();
                if (currentValue) {
                    sessionStorage.setItem(storageKey, currentValue);
                } else {
                    sessionStorage.removeItem(storageKey);
                }
            });
            
            window.addEventListener('beforeunload', function() {
                const currentValue = messageInput.value.trim();
                if (currentValue) {
                    sessionStorage.setItem(storageKey, currentValue);
                } else {
                    sessionStorage.removeItem(storageKey);
                }
            });
        }

        // 星評価の処理
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                updateStars();
                document.getElementById('rating-submit-btn').disabled = false;
                document.getElementById('score-error').style.display = 'none';
            });

            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
            });
        });

        document.getElementById('rating-stars').addEventListener('mouseleave', function() {
            updateStars();
        });

        // 評価フォーム送信
        document.getElementById('rating-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (selectedRating === 0) {
                document.getElementById('score-error').textContent = '評価を選択してください';
                document.getElementById('score-error').style.display = 'block';
                return;
            }

            document.getElementById('rating-submit-btn').disabled = true;
            document.getElementById('rating-submit-btn').textContent = '送信中...';

            // 取引完了処理
            const completeFormData = new FormData();
            completeFormData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("transactions.complete", $soldItem) }}', {
                method: 'POST',
                body: completeFormData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 評価送信
                    const ratingFormData = new FormData();
                    ratingFormData.append('_token', '{{ csrf_token() }}');
                    ratingFormData.append('score', selectedRating);

                    return fetch('{{ route("ratings.store", $soldItem) }}', {
                        method: 'POST',
                        body: ratingFormData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } else {
                    throw new Error(data.message || '取引完了に失敗しました');
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('取引が完了し、評価を送信しました');
                    closeRatingModalForce();
                    // 商品一覧画面に遷移
                    window.location.href = '/';
                } else {
                    throw new Error(data.message || '評価送信に失敗しました');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || '処理中にエラーが発生しました');
                
                document.getElementById('rating-submit-btn').disabled = false;
                document.getElementById('rating-submit-btn').textContent = '送信する';
                
                const completeBtn = document.querySelector('.complete-transaction-button');
                if (completeBtn) {
                    completeBtn.disabled = false;
                    completeBtn.textContent = '取引を完了する';
                }
            });
        });

        // モーダル外クリック無効化
        document.getElementById('rating-modal').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>