<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content' => 'required|string|max:400',
            'img_url' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => '本文を入力してください',
            'content.max' => '本文は400文字以内で入力してください',
            'img_url.image' =>  '「.png」または「.jpeg」形式でアップロードしてください',
            'img_url.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'img_url.max' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}

