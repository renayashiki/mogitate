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
            // 商品名: 入力必須
            'name' => ['required', 'string'],

            // 値段: 入力必須、整数値、0以上10000以下
            'price' => ['required', 'integer', 'min:0', 'max:10000'],

            // 季節: 選択必須（配列として存在し、各要素がseasonテーブルのIDであること）
            'seasons' => ['required', 'array', 'exists:seasons,id'],

            // 商品説明: 入力必須、最大120文字
            'description' => ['required', 'string', 'max:120'],

            // 画像: アップロード必須、MIMEタイプがjpegまたはpng
            'image' => ['required', 'file', 'mimes:jpeg,png'],
        ];
    }

    public function messages()
    {
        return[
            // 商品名
            'name.required' => '商品名を入力してください',

            // 値段
            'price.required' => '値段を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '0∼10000円以内で入力してください', // maxとminのエラーを同じメッセージに設定
            'price.max' => '0∼10000円以内で入力してください',

            // 季節
            'seasons.required' => '季節を選択してください',
            'seasons.array' => '季節を選択してください', // 選択されたにもかかわらず不正な形式の場合
            'seasons.exists' => '不正な季節が選択されました', // 実際には起こりにくいが不正対策用バリデーション

            // 商品説明
            'description.required' => '商品説明を入力してください',
            'description.max' => '120文字以内で入力してください',

            // 画像
            'image.required' => '商品画像を登録してください',
            'image.file' => '商品画像を登録してください', // ファイルとしてアップロードされていない場合
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    
    }
}
