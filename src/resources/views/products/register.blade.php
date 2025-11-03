@extends('layouts.app') 

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="form-container">
    <h2>商品登録</h2>
    
    {{-- FN008: 登録機能 --}}
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- 商品名 (必須) --}}
        <div class="form-group">
            <label for="name">商品名<span class="required">必須</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="商品名">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 価格 (必須, 数値, 0〜10000) --}}
        <div class="form-group">
            <label for="price">値段<span class="required">必須</span></label>
            <input type="number" id="price" name="price" value="{{ old('price') }}" placeholder="0〜10000円">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('price')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- 季節 (FN0012: 複数選択, 必須) --}}
        <div class="form-group">
            <label>季節<span class="required">必須</span></label>
            <div class="checkbox-group">
                @foreach ($seasons as $season)
                    <input type="checkbox" id="season_{{ $season->id }}" name="seasons[]" value="{{ $season->id }}" 
                        {{ in_array($season->id, old('seasons', [])) ? 'checked' : '' }}>
                    <label for="season_{{ $season->id }}">{{ $season->name }}</label>
                @endforeach
            </div>
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('seasons')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 商品説明 (必須, 120文字以内) --}}
        <div class="form-group">
            <label for="description">商品説明<span class="required">必須</span></label>
            <textarea id="description" name="description" rows="5" placeholder="商品の説明（120文字以内）">{{ old('description') }}</textarea>
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('description')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- 画像 (必須, .png/.jpeg) --}}
        <div class="form-group">
            <label for="image">画像<span class="required">必須</span></label>
            <input type="file" id="image" name="image">
            {{-- Form Requestで定義されたカスタムエラーメッセージを直接表示 --}}
            @error('image')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="button-group">
            {{-- 戻るボタン (FN008) --}}
            <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a> 
            {{-- 登録ボタン (FN008) --}}
            <button type="submit" class="btn-base btn-primary">登録</button>
        </div>
    </form>
</div>
@endsection
