@extends('layouts.app')

{{-- 共通CSSと合わせて、このページ固有のCSSを読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

{{-- メインコンテンツラッパー --}}
@section('content')
    <div class="main-content-wrapper">
        
        {{-- サイドバー (変更なし) --}}
        <aside class="sidebar">
            <h2 class="sidebar-title">商品一覧</h2> 

            {{-- 1. 検索フォーム (FN002) --}}
            <form action="{{ route('products.index') }}" method="GET" class="search-input-group">
                <input type="text" name="keyword" id="keyword" placeholder="商品名を入力" value="{{ request('keyword') }}">
                
                {{-- 検索ボタンのためのhiddenフィールド --}}
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                
                {{-- 黄色い検索ボタン --}}
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

            {{-- 適用中のソートフィルタボタンのロジック (変更なし) --}}
            @if (!empty($currentSortKey) && !empty($currentSortLabel))
                @php
                    $currentQueries = request()->query();
                    // 'sort' パラメータを削除
                    unset($currentQueries['sort']);
                    // 新しいURL（フィルタ解除URL）を生成。他のパラメータ(keyword等)は維持される
                    $resetUrl = route('products.index', $currentQueries);
                @endphp

                <div class="current-filter-group">
                    <a href="{{ $resetUrl }}" 
                       class="btn-base btn-filter-applied" 
                       title="クリックして並び替えを解除">
                        
                        {{-- コントローラーから渡された表示ラベル --}}
                        {{ $currentSortLabel }}
                        
                        {{-- 削除アイコン (Font Awesomeを想定) --}}
                        <i class="fas fa-times-circle filter-close-icon"></i>
                    </a>
                </div>
            @endif

        </aside>

        {{-- メインコンテンツエリア --}}
        <main class="content-area">
            <div class="container"> 
                
                {{-- 「商品を追加」ボタン (変更なし) --}}
                <div class="main-product-header">
                    <a href="{{ route('products.create') }}" class="btn-base btn-primary btn-add-product">
                        <i class="fas fa-plus mr-2"></i> +商品を追加
                    </a>
                </div>
                
                <div class="product-content-wrapper">
                    @if(request('keyword') || request('sort'))
                        <p class="result-count">{{ $products->total() }}件の商品が見つかりました。</p>
                    @endif

                    {{-- 商品一覧表示  --}}
                    <div class="product-card-grid">
                        @forelse ($products as $product)
                            <a href="{{ route('products.show', ['productId' => $product->id]) }}" class="product-card">
                                <div>
                                    {{-- ★★★ 画像パスのロジックを修正 ★★★ --}}
                                    @php
                                        // 1. アップロード画像（本番用）のパスを構築 (例: storage/products/banana.png)
                                        // 登録時に 'products/' プレフィックスを付けている前提
                                        $uploadedImagePath = asset('storage/' . $product->image);

                                        // 2. ダミー画像（開発用）のパスを構築 (例: images/dummy/banana.png)
                                        $dummyImagePath = asset('images/dummy/' . $product->image);
                                    @endphp

   {{-- データベースの image の値を使って、本番とダミーの両方のパスを試す --}}
                                        {{-- 最初の src で本番パスを試み、失敗したら onerror でダミーパスを試す --}}                                 <img src="{{ $uploadedImagePath }}"
                                        alt="{{ $product->name }}"
                                        {{-- 最初の画像（本番パス）のロードに失敗した場合の代替処理 --}}
                                        onerror="this.onerror=null; this.src='{{ $dummyImagePath }}'; this.onerror = function() { this.src='https://placehold.co/400x400/98c1d9/000?text=No+Image'; };"
                                    >
                                </div>
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
                    {{-- ページネーションリンクを動的に表示し、検索/並び替えパラメータを維持する --}}
                    <div class="pagination-links">
                        {{ $products->appends(['keyword' => request('keyword'), 'sort' => request('sort')])->links() }}
                    </div>

                </div>
            </div>
        </main>
    </div>
@endsection
