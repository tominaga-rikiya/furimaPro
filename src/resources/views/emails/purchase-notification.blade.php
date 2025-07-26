<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商品購入のお知らせ</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
        }
        .header { 
            background: #28a745; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .content { 
            padding: 30px 20px; 
        }
        .item-box { 
            background: #f8f9fa; 
            border: 1px solid #dee2e6; 
            border-radius: 8px; 
            padding: 20px; 
            margin: 20px 0; 
        }
        .price { 
            font-size: 24px; 
            font-weight: bold; 
            color: #dc3545; 
            margin: 10px 0; 
        }
        .buyer-info { 
            background: #e3f2fd; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 15px 0; 
        }
        .footer { 
            background: #f8f9fa; 
            padding: 20px; 
            text-align: center; 
            color: #666; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 商品が購入されました！</h1>
        </div>
        
        <div class="content">
            <p>{{ $item->user->name }} さん</p>
            
            <p>出品していただいた商品に購入者が現れました！</p>
            
            <div class="item-box">
                <h3>📦 {{ $item->name }}</h3>
                <div class="price">¥{{ number_format($item->price) }}</div>
                <p><strong>説明:</strong> {{ $item->description }}</p>
                @if($item->brand)
                <p><strong>ブランド:</strong> {{ $item->brand }}</p>
                @endif
            </div>
            
            <div class="buyer-info">
                <h4>👤 購入者情報</h4>
                <p><strong>お名前:</strong> {{ $buyer->name }}</p>
                <p><strong>購入日時:</strong> {{ now()->format('Y年m月d日 H:i') }}</p>
            </div>
            
            <p>取引を開始して、購入者と連絡を取り合ってください。</p>
            <p>商品の発送準備をお願いいたします。</p>
        </div>
        
        <div class="footer">
            <p>{{ config('app.name') }} チーム</p>
            <p>このメールは自動送信されています</p>
        </div>
    </div>
</body>
</html>