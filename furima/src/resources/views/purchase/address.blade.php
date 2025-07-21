@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
@include('components.header')

<form action="{{ route('purchase.update.address', $item_id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="postal_code">郵便番号</label>
        <input type="text" id="postal_code" name="postal_code" class="form-control" value="{{ old('postal_code', $profile->postal_code) }}" required>
        @error('postal_code')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="address">住所</label>
        <input type="text" id="address" name="address" class="form-control" value="{{ old('address', $profile->address) }}" required>
        @error('address')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="building_name">建物名</label>
        <input type="text" id="building_name" name="building_name" class="form-control" value="{{ old('building_name', $profile->building_name) }}">
        @error('building_name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">住所を更新</button>
</form>
