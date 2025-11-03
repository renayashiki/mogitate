{{-- Controllerのsearchアクションがこのビューを返すことを想定。
     PG01とデザインが共通のため、index.cssを読み込みます。--}}
@extends('layouts.app') 

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    {{-- ヘッダー部分 --}}
    <header class="header">
        <h1>検索結果一覧</h1>
        <a href="{{ route('products.create') }}" class="btn-base btn-primary">+ 商品を追加</a>
    </header>

    {{-- 検索・並び替えエリア (FN002, FN003, FN004) --}}
    <form action="{{ route('products.search') }}" method="GET" class="search-sort-form">
        <div class="search-input">
            <input type="text" name="keyword" placeholder="商品名で検索" value="{{ request('keyword') }}">
            <button type="submit" class="btn-search">検索</button>
        </div>

        <div class="sort-select">
            <select name="sort" onchange="this.form.submit()"> 
                <option value="" disabled selected>価格で並び替え</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>高い順に表示</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>低い順に表示</option>
            </select>
            
            {{-- 検索・並び替え条件のモーダル表示 (FN004, FN003-4) --}}
            @if(request('keyword') || request('sort'))
                <div class="search-conditions-modal">
                    @if(request('keyword'))
                        <span>検索: {{ request('keyword') }}</span>
                    @endif
                    @if(request('sort'))
                        <span>並び替え: 
                            {{ request('sort') == 'price_desc' ? '高い順' : '低い順' }}
                        </span>
                    @endif
                    <a href="{{ route('products.index') }}">×</a>
                </div>
            @endif
        </div>
    </form>

    <hr>

    {{-- 商品一覧表示 (FN001) --}}
    <div class="product-card-grid">
        @forelse ($products as $product)
            <a href="{{ route('products.show', ['productId' => $product->id]) }}" class="product-card">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <div class="card-info">
                    <h3>{{ $product->name }}</h3>
                    <p class="price">¥{{ number_format($product->price) }}</p>
                </div>
            </a>
        @empty
            <p>検索条件に一致する商品が見つかりませんでした。</p>
        @endforelse
    </div>

    {{-- ページネーション (FN006) --}}
    <div class="pagination-links">
        {{ $products->links() }}
    </div>
</div>
@endsection