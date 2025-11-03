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

            {{-- 1. 検索フォーム (FN002) --}}
            <form action="{{ route('products.index') }}" method="GET" class="search-input-group">
                {{-- 見出しは不要とのことなので、labelは削除 --}}
                <input type="text" name="keyword" id="keyword" placeholder="商品名を入力" value="{{ request('keyword') }}">
                
                {{-- 検索ボタンのためのhiddenフィールド --}}
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                
                {{-- ★修正1: 黄色い検索ボタンをここに戻す --}}
                <button type="submit" class="btn-base btn-search-yellow">検索</button>
            </form>

            {{-- 2. 並び替え機能 (FN003) --}}
            <div class="divider"></div>
            <form action="{{ route('products.index') }}" method="GET" class="sort-select-group">
                <label for="sort">価格順で表示</label>
                <select name="sort" id="sort" onchange="this.form.submit()"> 
                    <option value="" disabled {{ !request('sort') ? 'selected' : '' }}>価格で並び替え</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>高い順に表示</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>低い順に表示</option>
                </select>
                {{-- 並び替えのためのhiddenフィールド（検索キーワードを維持） --}}
                @if(request('keyword'))
                    <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                @endif
            </form>
            

            {{-- リセット機能 (見本にないためコメントアウトを維持) --}}
            {{-- <div class="divider"></div> --}}
            {{-- @if(request('keyword') || request('sort'))
                <!-- ... リセット条件表示のBladeコード ... -->
            @endif --}}
            
        </aside>

        {{-- メインコンテンツエリア --}}
        <main class="content-area">
            <div class="container"> 
                
                {{-- 「商品を追加」ボタン --}}
                <div class="main-product-header">
                    <a href="{{ route('products.create') }}" class="btn-base btn-primary btn-add-product">
                        <i class="fas fa-plus mr-2"></i> 商品を追加
                    </a>
                </div>
                
                <div class="product-content-wrapper">
                    
                    @if(request('keyword') || request('sort'))
                        <p class="result-count">{{ $products->total() }}件の商品が見つかりました。</p>
                    @endif

                    {{-- 商品一覧表示 (FN001) --}}
                    {{-- ★修正2: グリッドレイアウトはそのままに、CSSでカードサイズを小さくする --}}
                    <div class="product-card-grid">
                        @forelse ($products as $product)
                            <a href="{{ route('products.show', ['productId' => $product->id]) }}" class="product-card">
                                <div><img src="{{ asset('images/dummy/' . $product->image) }}" alt="{{ $product->name }}"></div>
                                <div class="card-info product-info-row">
                                    <h3 class="product-name">{{ $product->name }}</h3>
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
