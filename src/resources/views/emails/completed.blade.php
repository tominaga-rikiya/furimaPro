<!DOCTYPE html>
<html>
<head>
     <meta charset="utf-8">
    <title>取引完了のお知らせ</title>

    @section('css')
    <link rel="stylesheet" href="{{ asset('/css/email.css') }}">
    @endsection

</head>
<body>
    <div class="header">
        <h1>🎉 商品が購入されました！</h1>
    </div>
    
    <div class="content">
        <p>{{ $seller->name ?? 'お客様' }} さん</p>
        
        <p>おめでとうございます！あなたの出品した商品に購入者が現れました。</p>
        
        <div class="item-info">
            <h3>📦 {{ $item->name ?? '商品' }}</h3>
            <p><strong>価格:</strong> <span class="price">¥{{ number_format($item->price ?? 0) }}</span></p>
            <p><strong>説明:</strong> {{ $item->description ?? '' }}</p>
            @if(isset($item->brand) && $item->brand)
                <p><strong>ブランド:</strong> {{ $item->brand }}</p>
            @endif
        </div>
        
        <div class="buyer-info">
            <h4>👤 購入者情報</h4>
            <p><strong>お名前:</strong> {{ $buyer->name ?? '購入者' }}</p>
            <p><strong>購入日時:</strong> {{ $completedAt->format('Y年m月d日 H:i') }}</p>
        </div>
        
        <p><strong>次のステップ:</strong></p>
        <p>取引画面から購入者と連絡を取り、発送の準備をお願いします。</p>
    </div>
    
    <div class="footer">
        <hr>
        <p>{{ config('app.name', 'アプリ') }} チーム</p>
        <small>このメールは自動送信されています</small>
    </div>
</body>
</html>