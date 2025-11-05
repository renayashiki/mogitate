<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
  return redirect()->route('products.index');
});

// 商品一覧画面/検索・並び替え
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// 商品登録画面表示
Route::get('/products/register', [ProductController::class, 'create'])->name('products.create');

// 商品情報登録機能
Route::post('/products/register', [ProductController::class, 'store'])->name('products.store');

// 商品詳細画面
Route::get('/products/detail/{productId}', [ProductController::class, 'show'])->name('products.show');

// 検索機能
Route::get('/products/search', [ProductController::class, 'index'])->name('products.search');

// 商品更新画面表示
Route::get('/products/{productId}/update', [ProductController::class, 'edit'])->name('products.edit');

// 商品情報変更機能
Route::patch('/products/{productId}/update', [ProductController::class, 'update'])->name('products.update');

// 商品情報削除機能
Route::delete('/products/{productId}/delete', [ProductController::class, 'destroy'])->name('products.destroy');
