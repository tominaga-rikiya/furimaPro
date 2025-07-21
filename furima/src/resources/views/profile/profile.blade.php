@extends('layouts.app')

@section('content')
@include('components.header')
 <div class="container">
        <div class="profile-header">
            @if($user->profile)
                <img src="{{ $user->profile->profile_image ? Storage::url($user->profile->profile_image) : '/images/default-profile.png' }}" 
                     alt="プロフィール画像" 
                     class="profile-image" width=200px;>
            @else
                <img src="/images/default-profile.png" alt="デフォルトプロフィール画像" class="profile-image" >
            @endif
            
            <div class="profile-info">
                <h1 class="username">{{ $user->name }}</h1>
                <a href="{{ route('profile.edit') }}" class="edit-button">プロフィールを編集</a>
            </div>
        </div>

    <div class="profile-tabs">
        <a href="{{ route('profile.profile', ['tab' => 'listed']) }}" 
           class="tab-item {{ $activeTab === 'listed' ? 'active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('profile.profile', ['tab' => 'purchased']) }}" 
           class="tab-item {{ $activeTab === 'purchased' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    <div class="items-grid">
       @if($activeTab === 'listed')
    @forelse($listedItems as $item)
        <div class="item-card">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="item-image">
            <p class="item-name">{{ $item->name }}</p>
        </div>
    @empty
        <p>出品した商品はありません。</p>
    @endforelse
@else
   @forelse($purchasedItems as $item)
        <div class="item-card">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" class="item-image">
            <p class="item-name">{{ $item->name }}</p>
        </div>
    @empty
        <p>購入した商品はありません。</p>
    @endforelse
@endif
@endsection
