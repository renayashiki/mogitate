@extends('layouts.app')

{{-- 登録画面専用CSSを読み込む --}}
@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}"> 
@endsection

@section('content')

<div class="register-container">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="register-form">
        @csrf

        {{-- 2. 見出しを「商品登録」に修正し、位置を揃える --}}
        <h2 class="form-title">商品登録</h2>

        {{-- 【商品名】 --}}
        <div class="form-group">
            <label for="name">商品名 <span class="required-badge">必須</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 【値段】 --}}
        <div class="form-group">
            <label for="price">値段 <span class="required-badge">必須</span></label>
            <input type="number" name="price" id="price" value="{{ old('price') }}">
            @error('price')
                <span class="error-message">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- 【商品画像】 --}}
        <div class="form-group">
            <label for="image">商品画像 <span class="required-badge">必須</span></label>
            <input type="file" name="image" id="image">
            @error('image')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 【季節 (複数選択チェックボックス)】 --}}
        <div class="form-group">
            <label>季節 <span class="required-badge">必須</span></label>
            {{-- 3. CSSで〇に変更するため、クラス名を調整 --}}
            <div class="checkbox-group radio-style-group">
                @if (isset($seasons))
                    @foreach ($seasons as $season)
                        <label>
                            <input type="checkbox" 
                                name="seasons[]" 
                                value="{{ $season->id }}" 
                                {{ is_array(old('seasons')) && in_array($season->id, old('seasons')) ? 'checked' : '' }}
                            >
                            {{ $season->name }}
                        </label>
                    @endforeach
                @else
                    <p class="error-message">※季節情報が取得できませんでした。</p>
                @endif
            </div>
            @error('seasons') 
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        
        {{-- 【商品説明】 --}}
        <div class="form-group">
            <label for="description">商品説明 <span class="required-badge">必須</span></label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- 【ボタン】 --}}
        <div class="button-group-center">
            <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a> 
            <button type="submit" class="btn-base btn-primary">登録</button>
        </div>
    </form>
</div>
@endsection
