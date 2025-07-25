@extends('layouts.default')

@section('title','トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css')  }}">
@endsection

@section('content')

@include('components.header')
<div class="border">
    <ul class="border__list">
        <li><a href="{{ route('items.list', ['tab'=>'recommend', 'search'=>$search]) }}">おすすめ</a></li>
        @if(!auth()->guest())
        <li><a href="{{ route('items.list', ['tab'=>'mylist', 'search'=>$search]) }}">マイリスト</a></li>
        @endif
    </ul>
</div>
<div class="container">
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
</div>
@endsection