<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// 修正箇所: トップページURL（/）へのアクセスを商品一覧ページにリダイレクトするルートを追加
Route::get('/', function () {
  return redirect()->route('products.index');
});

// 商品一覧画面 (PG01) および 検索・並び替え (PG05/FN002, FN003, FN004)
// クエリパラメータ付きでアクセスされた場合も、ProductController@index で処理されます。
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// 商品登録画面表示 (PG04)
Route::get('/products/register', [ProductController::class, 'create'])->name('products.create');

// 商品情報登録機能 (FN008)
Route::post('/products/register', [ProductController::class, 'store'])->name('products.store');

// 商品詳細画面 (PG02/FN005)
Route::get('/products/detail/{productId}', [ProductController::class, 'show'])->name('products.show');

// 検索機能のルーティング
Route::get('/products/search', [ProductController::class, 'index'])->name('products.search');

// 商品更新画面表示 (PG03)
Route::get('/products/{productId}/update', [ProductController::class, 'edit'])->name('products.edit');
// 商品情報変更機能 (FN0013)
Route::patch('/products/{productId}/update', [ProductController::class, 'update'])->name('products.update');

// 商品情報削除機能 (PG06/FN0018)
Route::delete('/products/{productId}/delete', [ProductController::class, 'destroy'])->name('products.destroy');
