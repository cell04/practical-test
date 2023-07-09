<?php

namespace App\Http\Requests\Tweets;

use Illuminate\Foundation\Http\FormRequest;

class StoreTweetFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tweet'     => 'required|string|max:20000',
            'image' => 'required|mimes:png,jpeg,gif'
        ];
    }
}
