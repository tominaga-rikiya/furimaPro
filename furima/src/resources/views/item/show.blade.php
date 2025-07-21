@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
 @include('components.header')
<div class="item-data">
    
    <div class="item-header">
        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="item-image">
        <div class="item-details">
            <h1>{{ $item->name }}</h1>
            <p>ブランド名: {{ $item->brand_name }}</p>
            <p class="item-price">¥{{ number_format($item->price) }} (税込)</p>
            <p>
                <form action="{{ route('item.favorite', $item->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="_method" value="{{ $isFavorite ? 'DELETE' : 'POST' }}">
                    <button type="submit" class="star-btn {{ $isFavorite ? 'favorited' : '' }}">
                        <span>☆{{ $item->favorites()->count() }}</span>
                    </button>
                     <span class="comment-icon" >💬 {{ $item->comments()->count() }}</span>
                </form>
            </p>
        </div>
    </div>

    @if(!$item->is_sold)
        <form action="{{ route('purchase.create', $item->id) }}" method="GET">
            @csrf
            <button type="submit" class="btn btn-primary">購入手続きへ</button>
        </form>
    @else
   
    @endif

    <div class="item-description">
        <h2>商品説明</h2>
        <p>カラー: グレー</p>
      
        <p>{{ $item->description }}</p>
    </div>

<div class="item-info">
    <h2>商品の情報</h2>
    <p>カテゴリー:
        @if(!empty($item->category_ids))
            @foreach($item->category_ids as $categoryId)
                @php
                    $category = \App\Models\Category::find($categoryId);
                @endphp
                @if($category)
                    <span>{{ is_array($category->category) ? implode(', ', $category->category) : $category->category }}</span>
                @endif
            @endforeach
        @endif
    </p>
      <p>商品の状態: {{ $item->condition->condition }}</p>
</div>

    <div class="item-comments">
        <h2>コメント ({{ $item->comments->count() }})</h2>
        @forelse ($comments as $comment)
            <div class="comment">
                <p><strong>{{ $comment->user->name }}:</strong> {{ $comment->comment }}</p>
            </div>
        @empty
         
        @endforelse

        <form action="{{ route('item.comment', $item->id) }}" method="post">
            @csrf
            <textarea name="comment" rows="3" placeholder="商品へのコメントを入力してください..." ></textarea>
             <p class="comment_error-message">
          @error('comment')
          {{ $message }}
          @enderror
        </p>
            <button type="submit" class="btn">コメントを送信する</button>
        </form>
    </div>
</div>
@endsection
