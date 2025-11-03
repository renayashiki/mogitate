@extends('layouts.app')

{{-- 共通CSSと合わせて、このページ固有のCSSを読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

{{-- メインコンテンツラッパー --}}
@section('content')
    <div class="main-content-wrapper">
        
        {{-- サイドバー --}}
        <aside class="sidebar">
            <h2 class="sidebar-title">商品一覧</h2> 

            {{-- 検索フォーム (FN002) --}}
            <form action="{{ route('products.index') }}" method="GET" class="search-form-group">
                <label for="keyword">商品名で検索</label>
                <input type="text" name="keyword" id="keyword" placeholder="キーワードを入力" value="{{ request('keyword') }}">
                {{-- 黄色い検索ボタン --}}
                <button type="submit" class="btn-base btn-search btn-search-yellow">検索</button>
            </form>

            {{-- 並び替え機能 (FN003) --}}
            {{-- ★修正1: セレクトボックスを縦並びにするため、ラベルとセレクトボックスを分離 --}}
            <form action="{{ route('products.index') }}" method="GET" class="sort-select-group">
                <label for="sort">価格順で表示</label>
                {{-- <label>はそのまま残す --}}
                <select name="sort" id="sort" onchange="this.form.submit()"> 
                    <option value="" disabled {{ !request('sort') ? 'selected' : '' }}>価格で並び替え</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>高い順に表示</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>低い順に表示</option>
                </select>
                @if(request('keyword'))
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                @endif
            </form>
            {{-- ★修正1: 区切り線を追加 --}}
            <div class="divider"></div>

            {{-- 検索条件のリセット (見本にはないが、機能として残します) --}}
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
                    <a href="{{ route('products.index') }}" title="検索条件をリセット">×</a>
                </div>
            @endif
        </aside>

        {{-- メインコンテンツエリア --}}
        <main class="content-area">
            <div class="container"> 
                
                {{-- 「商品を追加」ボタン --}}
                <div class="main-product-header">
                    <a href="{{ route('products.create') }}" class="btn-base btn-primary btn-add-product">+ 商品を追加</a>
                </div>
                
                <div class="product-content-wrapper">
                    
                    @if(request('keyword') || request('sort'))
                        <p class="result-count">{{ $products->total() }}件の商品が見つかりました。</p>
                    @endif

                    {{-- 商品一覧表示 (FN001) --}}
                    <div class="product-card-grid">
                        @forelse ($products as $product)
                            {{-- 商品詳細ページへのリンク --}}
                            {{-- ★修正2: カードに枠線をつけるため、product-card に border-line クラスを追加 --}}
                            <a href="{{ route('products.show', ['productId' => $product->id]) }}" class="product-card border-line">
                                <img src="{{ asset('images/dummy/' . $product->image) }}" alt="{{ $product->name }}">
                                {{-- 商品名と価格を横並びにするためのラッパー --}}
                                <div class="card-info product-info-row">
                                    {{-- 商品名（左寄せ） --}}
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                    {{-- 価格（右寄せ） --}}
                                    <p class="price">¥{{ number_format($product->price) }}</p>
                                </div>
                            </a>
                        @empty
                            <p>商品が見つかりませんでした。</p>
                        @endforelse
                    </div>

                    {{-- ページネーション (FN006) --}}
                    <div class="pagination-links">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
