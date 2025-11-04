<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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

            // 季節: 選択必須
            'season_id' => ['required', 'exists:seasons,id'],

            // 商品説明: 入力必須、最大120文字
            'description' => ['required', 'string', 'max:120'],

            // 画像: 必須ではない（既に画像があるため）。ファイルが選択された場合のみバリデーションを行う。
            'image' => ['nullable', 'file', 'mimes:jpeg,png'],
        ];
    }

    public function messages()
    {
        return [
            // 商品名
            'name.required' => '商品名を入力してください',

            // 値段
            'price.required' => '値段を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '0∼10000円以内で入力してください',
            'price.max' => '0∼10000円以内で入力してください',

            // 季節
            'season_id.required' => '季節を選択してください',

            // 商品説明
            'description.required' => '商品説明を入力してください',
            'description.max' => '120文字以内で入力してください',

            // 画像 (requiredはnullableによりチェック不要)
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
