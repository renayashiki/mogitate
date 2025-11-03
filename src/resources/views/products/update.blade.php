@extends('layouts.app') 

{{-- PG03 専用の update.css を読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/update.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2>商品更新</h2>
    
    {{-- 削除ボタン (FN0018) --}}
    <div class="btn-delete-container">
        <form action="{{ route('products.destroy', ['productId' => $product->id]) }}" method="POST" onsubmit="return confirm('この商品を削除してもよろしいですか？');" style="display:inline-block;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete" title="削除">🗑️ この商品を削除する</button>
        </form>
    </div>

    {{-- FN0013: 変更機能 --}}
    <form action="{{ route('products.update', ['productId' => $product->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH') {{-- 更新にはPATCHメソッドを使用 --}}
        
        {{-- 現在の画像表示 (FN0013) --}}
        <div class="current-image-container">
            <h3>現在の画像</h3>
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
        </div>

        {{-- 商品名 --}}
        <div class="form-group">
            <label for="name">商品名</label>
            {{-- FN0013: 初期値表示 --}}
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" placeholder="商品名">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 価格 --}}
        <div class="form-group">
            <label for="price">値段</label>
            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" placeholder="0〜10000円">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('price')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 季節 (FN0016: 複数選択) --}}
        <div class="form-group">
            <label>季節</label>
            <div class="checkbox-group">
                @php 
                    // 現在の商品に紐づく季節IDを取得
                    $productSeasonIds = $product->seasons->pluck('id')->toArray();
                    // old()と現在の季節をマージしてチェック状態を決定
                    $checkedSeasons = old('seasons', $productSeasonIds);
                @endphp
                @foreach ($seasons as $season)
                    <input type="checkbox" id="season_{{ $season->id }}" name="seasons[]" value="{{ $season->id }}" 
                        {{ in_array($season->id, $checkedSeasons) ? 'checked' : '' }}>
                    <label for="season_{{ $season->id }}">{{ $season->name }}</label>
                @endforeach
            </div>
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('seasons')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- 商品説明 --}}
        <div class="form-group">
            <label for="description">商品説明</label>
            <textarea id="description" name="description" rows="5" placeholder="商品の説明（120文字以内）">{{ old('description', $product->description) }}</textarea>
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('description')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 画像 (FN0017: 新しい画像を選択) --}}
        <div class="form-group">
            <label for="image">新しい画像</label>
            <input type="file" id="image" name="image">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('image')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="button-group">
            {{-- 戻るボタン (FN0013) --}}
            <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a> 
            {{-- 変更を保存ボタン (FN0013) --}}
            <button type="submit" class="btn-base btn-primary">変更を保存</button>
        </div>
    </form>
</div>
@endsection
