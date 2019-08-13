<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            // 'title' => 'required|max:255',
            'content' => 'required',
            // 'user_id' => 'required|numeric',
            // 'book_id' => 'required|numeric',
            'post_image' => 'file|image',
        ];
    }

    public function messages() {
        return [
            'content.required' => '※入力してください',
            'content.file' => '※アップロードに失敗しました。別の画像で試してみてください',
            'post_image.image' => '※ファイルはjpg、png、bmp、gif、svgのみアップロード可能です',
            'post_image.max' => '※画像が大きすぎます。縮小するか、別の画像をアップロードしてください',
        ];
    }
}
