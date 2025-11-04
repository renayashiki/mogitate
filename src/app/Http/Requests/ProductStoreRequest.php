<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // a. 全ての項目が入力必須
            'name' => 'required|string|max:255',

            // b. 値段は数値、0円以上〜10000円以内
            'price' => 'required|numeric|min:0|max:10000',

            // c. 画像の拡張子は「.png」もしくは「.jpeg」形式でのみアップロード可能
            // 例外の「2MB以内」のルールも追加（2048KB）
            'image' => [
                'required',
                'image',
                'mimes:png,jpeg', // 拡張子をpngとjpegに限定
                'max:2048'     // 2MB (2048KB) を上限とする
            ],

            // 季節は必須、配列形式で、かつDBに存在するIDであること
            'seasons' => 'required|array|exists:seasons,id',

            // a. 必須かつ d. 入力文字数は120文字以内
            'description' => 'required|string|max:120',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // 商品名
            'name.required' => '商品名を入力してください',

            // 値段
            'price.required' => '値段を入力してください',
            'price.numeric' => '値段は数値で入力してください', 
            'price.min' => '値段は0円以上にしてください',
            'price.max' => '値段は10000円以内で入力してください', 

            // 画像
            'image.required' => '画像を登録してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください', 
            'image.max' => 'ファイルサイズは2MB以内の画像を選択してください', // 例外として維持

            // 季節
            'seasons.required' => '季節を選択してください',

            // 商品説明
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明の入力文字数は120文字以内で入力してください',
        ];
    }
}
