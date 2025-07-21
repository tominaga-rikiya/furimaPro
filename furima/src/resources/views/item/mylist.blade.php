@extends('layout.app')

@foreach ($items as $item)
    <div class="item">
        <img src="{{ $item->image->url }}" alt="{{ $item->name }}" class="item-image">
        <h3>{{ $item->name }}</h3>
      
        @if ($item->purchases()->where('user_id', auth()->id())->where('status', 'completed')->exists())
            <span class="sold">Sold</span>
        @endif
    </div>
@endforeach

@endsection
