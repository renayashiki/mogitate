<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // ★ Logファサードを明示的にインポート

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

        // FN006: ページネーション（1ページあたり6件）
        $products = $query->paginate(6);

        // 検索結果画面 (PG05) のURLを生成するために、検索・ソートパラメータを付加
        $products->appends($request->only(['keyword', 'sort']));

        // ★★★ 修正箇所: getCurrentSortLabelのロジックをここに直接組み込みます ★★★

        $currentSortKey = $sort;
        $currentSortLabel = null;

        $labels = [
            'price_desc' => '価格が高い順',
            'price_asc' => '価格が低い順',
        ];

        // ソートキーに対応するラベルを取得
        if (isset($labels[$currentSortKey])) {
            $currentSortLabel = $labels[$currentSortKey];
        }

        // 検索結果と一覧画面は同じビューを共有
        return view('products.index', compact('products', 'currentSortKey', 'currentSortLabel'));
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
            // 'products'フォルダに画像を保存し、'public'ディスクを使用
            $imagePath = $request->file('image')->store('products', 'public');

            // 商品の登録
            $product = Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                // storage/products/ファイル名 の形式で保存
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
            Log::error('商品登録エラー: ' . $e->getMessage()); // ★ ログを追加
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

        // show.blade.phpが詳細・更新のUIを兼ねるため、季節の選択肢を渡す
        $seasons = Season::all();
        // 現在の商品に紐づく季節IDの配列を作成（ラジオボタンの選択状態に必要）
        $productSeasonIds = $product->seasons->pluck('id')->toArray();

        // 既存のview名が'products.show'であることを確認
        return view('products.show', compact('product', 'seasons', 'productSeasonIds'));
    }

    /**
     * PG03: 商品更新画面を表示します。(★ 今回の要件ではshowが兼ねる可能性あり。このメソッドは未使用のまま維持。)
     */
    public function edit(string $productId)
    {
        // FN0013: 既存商品データを取得
        $product = Product::with('seasons')->findOrFail($productId);
        $seasons = Season::all(); // FN0016: 季節の選択肢

        // 現在の商品に紐づく季節IDの配列を作成
        $productSeasonIds = $product->seasons->pluck('id')->toArray();

        // 既存のビュー名が'products.update'の場合
        return view('products.update', compact('product', 'seasons', 'productSeasonIds'));
    }

    /**
     * FN0013: 商品の変更を保存します。
     * ★引数をRequestからProductUpdateRequestに変更
     * * @param  \App\Http\Requests\ProductUpdateRequest  $request
     * @param  string $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductUpdateRequest $request, string $productId)
    {
        $product = Product::findOrFail($productId);
        // FormRequestが自動でバリデーションを行う
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
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                // 新しい画像を保存
                $updateData['image'] = $request->file('image')->store('products', 'public');
            }

            // 商品情報を更新
            $product->update($updateData);

            // 季節の関連付けを更新 (FN0016)
            // ★ Form Requestで'season_id'を使う前提で、こちらでは'seasons'ではなく'season_id'を参照
            $product->seasons()->sync([$validated['season_id']]);

            DB::commit();

            // FN0015: 詳細画面にリダイレクト
            return redirect()->route('products.show', ['productId' => $product->id])
                ->with('success', '商品情報が正常に更新されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('商品更新エラー: ' . $e->getMessage()); // ★ ログを追加
            return back()->withInput()->withErrors(['db_error' => '商品の更新中にエラーが発生しました。']);
        }
    }

    /**
     * FN0018: 商品を削除します。
     * * @param  string $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $productId)
    {
        $product = Product::findOrFail($productId);

        DB::beginTransaction();
        try {
            // 画像ファイルの削除
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // 商品と季節の関連付けを削除
            $product->seasons()->detach();

            // 商品を削除
            $product->delete();

            DB::commit();

            // FN0019: 一覧画面にリダイレクト
            return redirect()->route('products.index')
                ->with('success', '商品が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('商品削除エラー: ' . $e->getMessage()); // ★ ログを追加
            return back()->withErrors(['db_error' => '商品の削除中にエラーが発生しました。']);
        }
    }
}
