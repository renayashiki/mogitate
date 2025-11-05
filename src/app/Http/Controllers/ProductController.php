<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Product;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /* 商品一覧画面と検索結果一覧画面
     */
    public function index(Request $request)
    {
        $query = Product::with('seasons');

        // 商品名検索
        if ($request->filled('keyword')) {
            $query->where('name', 'LIKE', "%{$request->keyword}%");
        }

        //並び替え
        $sort = $request->input('sort');
        if ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        //ページネーション（1ページあたり6件）
        $products = $query->paginate(6);

        // 検索結果画面
        $products->appends($request->only(['keyword', 'sort']));


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

    /*商品登録画面を表示*/
    public function create()
    {
        // FN007: 季節の選択肢を取得
        $seasons = Season::all();
        return view('products.register', compact('seasons'));
    }

    /*商品をデータベースに登録*/
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // 画像の保存
            $imagePath = $request->file('image')->store('products', 'public');

            // 商品の登録
            $product = Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'description' => $validated['description'],
                // storage/products/ファイル名の形式で保存
                'image' => $imagePath,
            ]);

            // 季節の関連付け
            $product->seasons()->attach($validated['seasons']);

            DB::commit();

            //詳細画面にリダイレクト
            return redirect()->route('products.index')
                ->with('success', '商品が正常に登録されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('商品登録エラー: ' . $e->getMessage());
            return back()->withInput()->withErrors(['db_error' => '商品の登録中にエラーが発生しました。']);
        }
    }

    /*商品詳細画面を表示*/
    public function show(string $productId)
    {
        //商品データを取得
        $product = Product::with('seasons')->findOrFail($productId);

        //季節の選択肢を渡す
        $seasons = Season::all();
        // 現在の商品に紐づく季節IDの配列を作成
        $productSeasonIds = $product->seasons->pluck('id')->toArray();

        return view('products.show', compact('product', 'seasons', 'productSeasonIds'));
    }

    /*商品更新画面を表示*/
    public function edit(string $productId)
    {
        //既存商品データを取得
        $product = Product::with('seasons')->findOrFail($productId);
        $seasons = Season::all();

        // 現在の商品に紐づく季節IDの配列を作成
        $productSeasonIds = $product->seasons->pluck('id')->toArray();


        return view('products.update', compact('product', 'seasons', 'productSeasonIds'));
    }

    /**商品の変更を保存**/
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

            //新しい画像がアップロードされた場合のみ処理
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

            $product->seasons()->sync($validated['seasons'] ?? []);

            DB::commit();

            // 商品一覧にリダイレクト
            return redirect()->route('products.index')
                ->with('success', '商品情報が正常に更新されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('商品更新エラー: ' . $e->getMessage());
            return back()->withInput()->withErrors(['db_error' => '商品の更新中にエラーが発生しました。']);
        }
    }

    /*商品を削除*/
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

            // 一覧画面にリダイレクト
            return redirect()->route('products.index')
                ->with('success', '商品が正常に削除されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('商品削除エラー: ' . $e->getMessage());
            return back()->withErrors(['db_error' => '商品の削除中にエラーが発生しました。']);
        }
    }
}
