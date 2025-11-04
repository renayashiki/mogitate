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
            // arrayとexistsは不正な値が入った場合の対策で、見た目のrequiredエラーは 'required'で処理
            'seasons' => ['required', 'array', 'exists:seasons,id'],

            // 商品説明: 入力必須、最大120文字
            'description' => ['required', 'string', 'max:120'],

            // 画像: アップロード必須、MIMEタイプがjpegまたはpng
            'image' => ['required', 'file', 'mimes:jpeg,png'],
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
            'price.integer' => '数値で入力してください',
            // maxとminのエラーを同じメッセージに設定
            'price.min' => '0∼10000円以内で入力してください',
            'price.max' => '0∼10000円以内で入力してください',

            // 季節
            // 'required'で選択されていないエラーを捕捉する
            'seasons.required' => '季節を選択してください',
            // 'array'や'exists'エラーはユーザーには見せないが、念のため設定
            'seasons.array' => '季節を選択してください',
            'seasons.exists' => '不正な季節が選択されました',

            // 商品説明
            'description.required' => '商品説明を入力してください',
            'description.max' => '120文字以内で入力してください',

            // 画像
            'image.required' => '画像を登録してください',
            // 'file'エラーは必須と統合
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            // 'mimes'ルールは画像がアップロードされたが拡張子が不正な場合にのみ発火する
        ];
    }
}
