<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserUpdateRequest extends FormRequest
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
        if (Auth::id()) {
            $unique_email = '|unique:users,email,' . Auth::id() . ',id';
        } else {
            $unique_email = '|unique:users,email';
        }

        if (Auth::id()) {
            $unique_name_id = '|unique:users,name_id,' . Auth::id() . ',id';
        } else {
            $unique_name_id = '|unique:users,name_id';
        }

        return [
            'name' => 'string|max:255',
            'email' => 'string|email|max:255' . $unique_email,
            'name_id' => 'string' . $unique_name_id,
            'password' => 'string|confirmed|min:8',
        ];
    }
}
