@extends('layouts.app') 

{{-- PG02 専用の show.css を読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2>商品詳細</h2>
    
    {{-- FN005: 商品詳細情報表示 --}}
    
    <div class="current-image-container">
        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
    </div>

    <div class="detail-info">
        <div class="detail-row">
            <strong>商品名:</strong> <span>{{ $product->name }}</span>
        </div>
        <div class="detail-row">
            <strong>値段:</strong> <span class="price">¥{{ number_format($product->price) }}</span>
        </div>
        <div class="detail-row">
            <strong>季節:</strong> 
            <span>
                @foreach ($product->seasons as $season)
                    {{ $season->name }}@if (!$loop->last), @endif
                @endforeach
            </span>
        </div>
        <div class="detail-row">
            <strong>商品説明:</strong> 
            <div class="description-box">{{ $product->description }}</div>
        </div>
    </div>
    
    <div class="button-group">
        {{-- 戻るボタン --}}
        <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a> 
        
        {{-- 更新ページへの遷移ボタン (PG03) --}}
        <a href="{{ route('products.edit', ['productId' => $product->id]) }}" class="btn-base btn-primary">変更・更新</a>
    </div>
    
</div>
@endsection