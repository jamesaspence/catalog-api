<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * @property string $email
 * @property string $external_id
 */
class RegisterRequest extends FormRequest
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
            'email' => 'required|email',
            'external_id' => 'required|string',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => Str::lower($this->email),
        ]);
    }
}
