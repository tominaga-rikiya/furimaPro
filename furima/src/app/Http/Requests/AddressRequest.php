<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'postal_code.required' => '郵便番号は必須です。',
            'postal_code.regex' => '郵便番号は「XXX-XXXX」の形式で入力してください。',
            'address.required' => '住所は必須です。',
            'building_name.required' => '建物名は必須です。',
            'building_name.string' => '建物名は文字列で入力してください。',
            'profile_image.image' => '画像ファイルをアップロードしてください。',
            'profile_image.mimes' => '画像はjpeg, png, jpg形式でアップロードしてください。',
        ];
    }
}
