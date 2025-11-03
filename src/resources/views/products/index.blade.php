@extends('layouts.app')

{{-- 共通CSSと合わせて、このページ固有のCSSを読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

{{-- メインコンテンツラッパー --}}
@section('content')
    <div class="main-content-wrapper">
        
        {{-- ★レイアウト修正済み: サイドバーのコンテンツを独立したブロックとして保持 --}}
        <aside class="sidebar">
            <h2 class="sidebar-title">商品一覧</h2> 

            {{-- 検索フォーム (FN002) --}}
            <form action="{{ route('products.index') }}" method="GET" class="search-form-group">
                <label for="keyword">商品名で検索</label>
                <input type="text" name="keyword" id="keyword" placeholder="キーワードを入力" value="{{ request('keyword') }}">
                <button type="submit" class="btn-base btn-search">検索</button>
            </form>

            {{-- 並び替え機能 (FN003) --}}
            <form action="{{ route('products.index') }}" method="GET" class="sort-select-group">
                <label for="sort">価格帯で表示</label>
                <select name="sort" id="sort" onchange="this.form.submit()"> 
                    <option value="" disabled {{ !request('sort') ? 'selected' : '' }}>選択してください</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>高い順に表示</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>低い順に表示</option>
                </select>
                {{-- 検索キーワードも引き継ぐ --}}
                @if(request('keyword'))
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                @endif
            </form>

            {{-- 検索条件のリセット --}}
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
                
                {{-- ★レイアウト修正済み: 「商品を追加」ボタンのスタイル適用を確実にする --}}
                <div class="main-product-header">
                    <a href="{{ route('products.create') }}" class="btn-base btn-primary">+ 商品を追加</a>
                </div>
                
                <div class="product-content-wrapper">
                    
                    {{-- 検索結果件数表示 (PG05) --}}
                    @if(request('keyword') || request('sort'))
                        <p class="result-count">{{ $products->total() }}件の商品が見つかりました。</p>
                    @endif

                    {{-- 商品一覧表示 (FN001) --}}
                    <div class="product-card-grid">
                        @forelse ($products as $product)
                            {{-- 商品詳細ページへのリンク --}}
                            <a href="{{ route('products.show', ['productId' => $product->id]) }}" class="product-card">
                                {{-- ★画像パス最終修正: Seederでファイル名のみを登録したため、asset('images/dummy/' . ファイル名) を参照 --}}
                                <img src="{{ asset('images/dummy/' . $product->image) }}" alt="{{ $product->name }}">
                                <div class="card-info">
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