@extends('layouts.app')

{{-- PG02/PG03 共通の show.css を読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
    {{-- Font Awesome (ゴミ箱アイコン用) を読み込む --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
{{-- 1. 商品一覧>バナナを画像の真上に配置 --}}
<div class="breadcrumb-container">
    <a href="{{ route('products.index') }}" class="breadcrumb-link">商品一覧</a>
    <span class="breadcrumb-separator">></span>
    <span class="breadcrumb-current">{{ $product->name }}</span>
</div>

{{-- 1. 白い背景カードをなくすため、formタグの最上位クラスを変更 --}}
<form action="{{ route('products.update', ['productId' => $product->id]) }}" method="POST" enctype="multipart/form-data" class="product-detail-form">
    @csrf
    {{-- ★ここをPUTからPATCHに変更します。ルーターがPATCHを要求しているためです。 --}}
    @method('PATCH') 

    <div class="form-content-wrapper">
        {{-- 【左側】商品画像エリア (FN005, FN0017) --}}
        <div class="image-area">
            {{-- 4. 商品画像という見出しは不要 --}}
            <div class="product-image-display">
                @php
                    $uploadedImagePath = asset('storage/' . $product->image);
                    $dummyImagePath = asset('images/dummy/' . $product->image);
                @endphp
                
                <img src="{{ $uploadedImagePath }}"
                    alt="{{ $product->name }}"
                    class="product-actual-image"
                    {{-- Bladeパースエラー修正済み：onerror内のJavaScript文字列をe()で安全にエスケープ --}}
                    onerror="this.onerror=null; this.src='{{ $dummyImagePath }}'; this.onerror = function() { this.src='https://placehold.co/250x250/98c1d9/000?text={{ e($product->name) }}'; };"
                >
            </div>
            
            {{-- ★【修正点2】ファイル選択ボタンとファイル名を横並びにするための新しいコンテナ --}}
            <div class="file-upload-row">
                <input type="file" name="image" id="image" class="file-input-hidden">
                <label for="image" class="btn-base btn-file-label">ファイルを選択</label>
                
                <p class="file-instruction current-filename-display">
                    {{ $product->image ?: '画像未登録' }}
                </p>
            </div>

            @error('image')
                {{-- エラーメッセージを画像エリアの直下に移動 --}}
                <div class="form-group error-message-group">
                    <span class="error-message">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- 5. 商品名・値段・季節を全体的に下にずらして画像の下線のラインに合わせて配置 --}}
        <div class="input-fields-area">
            {{-- 商品名 (input) --}}
            <div class="form-group">
                <label for="name" class="input-label">商品名</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="text-input">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- 値段 (input) --}}
            <div class="form-group">
                <label for="price" class="input-label">値段</label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" class="text-input">
                @error('price')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            {{-- 季節 (ラジオボタン) --}}
            <div class="form-group radio-group-container">
                <label class="input-label">季節</label>
                <div class="radio-options-wrapper">
                    @if (isset($seasons))
                        @foreach ($seasons as $season)
                            @php
                                // $productSeasonIds は 'show' メソッドで渡されています
                                $isChecked = in_array($season->id, $productSeasonIds ?? []);
                            @endphp
                            <label class="radio-custom-label">
                                <input type="radio" 
                                    name="season_id" 
                                    value="{{ $season->id }}" 
                                    {{ old('season_id', $isChecked ? $season->id : null) == $season->id ? 'checked' : '' }}
                                >
                                <span class="radio-custom-circle"></span> {{ $season->name }}
                            </label>
                        @endforeach
                    @else
                        <p class="error-message">※季節情報が取得できませんでした。</p>
                    @endif
                </div>
                @error('season_id') 
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    
    {{-- 5. 商品説明は画像と商品名・値段・季節の下に横幅を合わせて、長方形になるように配置 --}}
    <div class="description-area">
        <div class="form-group">
            <label for="description" class="input-label">商品説明</label>
            <textarea name="description" id="description" class="textarea-input-full">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- 6. 商品説明の下に中央揃えで戻るボタンと変更を保存ボタンを配置 --}}
    <div class="button-group-bottom">
        <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a> 
        <button type="submit" class="btn-base btn-primary-update">変更を保存</button>
        {{-- 7. ゴミ箱マークは枠や背景なし/7マーク自体を赤に --}}
        <button type="button" class="btn-delete-icon" onclick="document.getElementById('delete-form').submit()">
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</form>

{{-- 削除用フォーム (PG06 /products/{:productId}/delete へのリクエスト) --}}
<form id="delete-form" action="{{ route('products.destroy', ['productId' => $product->id]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
