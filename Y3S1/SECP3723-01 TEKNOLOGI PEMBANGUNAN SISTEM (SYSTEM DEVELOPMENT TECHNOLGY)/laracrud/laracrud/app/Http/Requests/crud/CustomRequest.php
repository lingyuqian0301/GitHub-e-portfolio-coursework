<?php

namespace App\Http\Requests\crud;

use Illuminate\Foundation\Http\FormRequest;

class CustomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Change false to true
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:6|max:255',
            'email' => 'required|string|min:12|max:255',
        ];
    }
}