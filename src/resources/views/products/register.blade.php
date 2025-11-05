@extends('layouts.app')

@section('page_styles')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

<div class="register-container">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="register-form">
        @csrf

        <h2 class="form-title">商品登録</h2>

        {{-- 商品名 --}}
        <div class="form-group">
            <label for="name">商品名 <span class="required-badge">必須</span></label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="商品名を入力">

            @if ($errors->has('name'))
                @foreach ($errors->get('name') as $message)
                    <span class="error-message">{{ $message }}</span>
                @endforeach
            @endif
        </div>

        {{-- 値段 --}}
        <div class="form-group">
            <label for="price">値段 <span class="required-badge">必須</span></label>
            <input type="text" name="price" id="price" value="{{ old('price') }}" placeholder="値段を入力">

            @if ($errors->has('price'))
                @foreach ($errors->get('price') as $message)
                    <span class="error-message">{{ $message }}</span>
                @endforeach
            @endif
        </div>

        {{-- 商品画像 --}}
        <div class="form-group">
            <label for="image">商品画像 <span class="required-badge">必須</span></label>
            <input type="file" name="image" id="image">

            @if ($errors->has('image'))
                @foreach ($errors->get('image') as $message)
                    <span class="error-message">{{ $message }}</span>
                @endforeach
            @endif
        </div>

        {{-- 季節 (複数選択チェックボックス) --}}
        <div class="form-group">
            <label>季節 <span class="required-badge">必須</span></label>
            <div class="checkbox-group radio-style-group">
                @if (isset($seasons))
                    @foreach ($seasons as $season)
                        <label>
                            <input type="checkbox"
                                name="seasons[]"
                                value="{{ $season->id }}"
                                {{ is_array(old('seasons')) && in_array($season->id, old('seasons')) ? 'checked' : '' }}
                            >
                            {{-- 丸型UIを表示するための<span> --}}
                            <span class="radio-text-style">{{ $season->name }}</span>
                        </label>
                    @endforeach
                @else
                    <p class="error-message">※季節情報が取得できませんでした。</p>
                @endif
            </div>

            @if ($errors->has('seasons'))
                @foreach ($errors->get('seasons') as $message)
                    <span class="error-message">{{ $message }}</span>
                @endforeach
            @endif
        </div>

        {{-- 商品説明 --}}
        <div class="form-group">
            <label for="description">商品説明 <span class="required-badge">必須</span></label>
            {{-- プレースホルダーを追加 --}}
            <textarea name="description" id="description" placeholder="商品の説明を入力">{{ old('description') }}</textarea>

            @if ($errors->has('description'))
                @foreach ($errors->get('description') as $message)
                    <span class="error-message">{{ $message }}</span>
                @endforeach
            @endif
        </div>

        {{-- ボタン --}}
        <div class="button-group-center">
            <a href="{{ route('products.index') }}" class="btn-base btn-secondary">戻る</a>
            <button type="submit" class="btn-base btn-primary">登録</button>
        </div>
    </form>
</div>
@endsection