@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('content')
@include('components.header')
<div class="container">
    <h1>プロフィール設定</h1>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            
            @if(isset($profile) && $profile->profile_image)
                <div class="current-image">
                    <img src="{{ Storage::url($profile->profile_image) }}" alt="現在のプロフィール画像" width=200px;>
                </div>
            @endif

            <label for="profile_image" class="custom-file-upload">
        画像を選択する
    </label>

           <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
            @error('profile_image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>ユーザー名</label>
            <input  type="text" name="name" value="{{ old('name', $user->name) }}" >
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>郵便番号</label>
            <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}">
            @error('postal_code')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>住所</label>
            <input type="text" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>建物名</label>
            <input type="text" name="building_name" value="{{ old('building_name', $profile->building_name ?? '') }}">
            @error('building_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="submit-btn">更新する</button>
        </div>
    </form>
</div>
@endsection