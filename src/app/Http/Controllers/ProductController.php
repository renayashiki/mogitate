<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * PG01: 商品一覧画面とPG05: 検索結果一覧画面を表示します。
     */
    public function index(Request $request)
    {
        $query = Product::with('seasons');

        // FN002: 商品名検索
        if ($request->filled('keyword')) {
            $query->where('name', 'LIKE', "%{$request->keyword}%");
        }

        // FN003: 並び替え
        $sort = $request->input('sort');
        if ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } else {
            // デフォルトはID順（新しいものが上）
            $query->orderBy('id', 'desc');
        }

        // FN006: ページネーション（1ページあたり9件）
        $products = $query->paginate(6);

        // 検索結果画面 (PG05) のURLを生成するために、検索・ソートパラメータを付加
        $products->appends($request->only(['keyword', 'sort']));

        // 検索結果と一覧画面は同じビューを共有
        return view('products.index', compact('products'));
    }

    /**
     * PG04: 商品登録画面を表示します。
     */
    public function create()
    {
        // FN007: 季節の選択肢を取得
        $seasons = Season::all();
        return view('products.register', compact('seasons'));
    }

    /**
     * FN008: 商品をデータベースに登録します。
     * ★引数をRequestからProductStoreRequestに変更
     */
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 画像の保存 (FN0010)
            $imagePath = $request->file('image')->store('products', 'public');

            // 商品の登録
            $product = Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                'image' => $imagePath,
            ]);

            // 季節の関連付け (FN0012)
            $product->seasons()->attach($validated['seasons']);

            DB::commit();

            // FN009: 詳細画面にリダイレクト
            return redirect()->route('products.show', ['productId' => $product->id])
                ->with('success', '商品が正常に登録されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            // ログ出力など適切なエラー処理
            return back()->withInput()->withErrors(['db_error' => '商品の登録中にエラーが発生しました。']);
        }
    }

    /**
     * PG02: 商品詳細画面を表示します。
     */
    public function show(string $productId)
    {
        // FN005: 商品データを取得
        $product = Product::with('seasons')->findOrFail($productId);
        return view('products.show', compact('product'));
    }

    /**
     * PG03: 商品更新画面を表示します。
     */
    public function edit(string $productId)
    {
        // FN0013: 既存商品データを取得
        $product = Product::with('seasons')->findOrFail($productId);
        $seasons = Season::all(); // FN0016: 季節の選択肢

        // 現在の商品に紐づく季節IDの配列を作成
        $productSeasonIds = $product->seasons->pluck('id')->toArray();

        return view('products.update', compact('product', 'seasons', 'productSeasonIds'));
    }

    /**
     * FN0013: 商品の変更を保存します。
     * ★引数をRequestからProductUpdateRequestに変更
     */
    public function update(ProductUpdateRequest $request, string $productId)
    {
        $product = Product::findOrFail($productId);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $updateData = [
                'name' => $validated['name'],
                'price' => $validated['price'],
                'description' => $validated['description'],
            ];

            // FN0017: 新しい画像がアップロードされた場合のみ処理
            if ($request->hasFile('image')) {
                // 古い画像を削除
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                // 新しい画像を保存
                $updateData['image'] = $request->file('image')->store('products', 'public');
            }

            // 商品情報を更新
            $product->update($updateData);

            // 季節の関連付けを更新 (FN0016)
            $product->seasons()->sync($validated['seasons']);

            DB::commit();

            // FN0015: 詳細画面にリダイレクト
            return redirect()->route('products.show', ['productId' => $product->id])
                ->with('success', '商品情報が正常に更新されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['db_error' => '商品の更新中にエラーが発生しました。']);
        }
    }

    /**
     * FN0018: 商品を削除します。
     */
    public function destroy(string $productId)
    {
        $product = Product::findOrFail($productId);

        DB::beginTransaction();
        try {
            // 画像ファイルの削除
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // 商品と季節の関連付けを削除（不要だが安全のため）
            $product->seasons()->detach();

            // 商品を削除
            $product->delete();

            DB::commit();

            // FN0019: 一覧画面にリダイレクト
            return redirect()->route('products.index')
                ->with('success', '商品が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['db_error' => '商品の削除中にエラーが発生しました。']);
        }
    }
}
