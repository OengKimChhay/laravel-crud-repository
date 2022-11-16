<?php

namespace App\Modules\Auth\Request;
use Illuminate\Validation\Rule;

class AuthUpdateRequest extends AuthRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $exceptId = request()->route('auth'); // auth is route params
        return [
            ...parent::rules(),
            'email' => ['required', Rule::unique('users', 'email')->ignore($exceptId)]
        ];
    }
}
