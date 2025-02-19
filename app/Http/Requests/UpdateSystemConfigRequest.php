<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemConfigRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'configs' => 'required|array',
            'configs.*' => 'nullable'
        ];
    }
} 