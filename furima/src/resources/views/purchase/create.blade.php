@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
 @include('components.header')
<div class="purchase-form">
    <div class="item-details">
        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="item-image" width="200px">
        <h3>{{ $item->name }}</h3>
        <p>価格: ¥{{ number_format($item->price) }}</p>

    </div>

    <form action="{{ route('purchase.store', $item_id) }}" method="post">
        @csrf
        <div class="form-group">
            <label for="payment">支払い方法</label>
            <select name="payment_method" id="payment"  class="form-control" onchange="this.form.submit()" required>
                <option value="credit_card" {{ old('payment_method', $paymentMethod) == 'credit_card' ? 'selected' : '' }}>カード払い</option>
                <option value="convenience_store" {{ old('payment_method', $paymentMethod) == 'convenience_store' ? 'selected' : '' }}>コンビニ払い</option>
            </select>
        </div>

           <div class="form-group">
            <label for="address">配送先</label>
            <h2>〒{{ $profile->postal_code }}</h2>
            <h3>{{ $profile->address }}</h3>
            <h3>{{ $profile->building_name }}</h3>
            <input type="hidden" name="address" value="{{ $profile->address }}">
        </div>
    
    <a href="{{ route('purchase.address', $item_id )}}" class="btn btn-link">変更する</a>

       <div class="form-group">
            <label for="subtotal">小計</label>
            <p id="subtotal">
                ¥{{ number_format($subtotal) }}
                <br>
                {{ old('payment_method', $paymentMethod) == 'credit_card' ? 'カード払い' : 'コンビニ払い' }}
            </p>
        </div>

        <button type="submit" class="btn btn-primary">購入する</button>
    </form>
</div>
@endsection
