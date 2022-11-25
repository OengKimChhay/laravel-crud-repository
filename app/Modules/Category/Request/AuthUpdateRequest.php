<?php

namespace App\Modules\Category\Request;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends CategoryRequest
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
        $exceptId = request()->route('category'); // category is route params
        return [
            ...parent::rules(),
            'name' => ['required', Rule::unique('category', 'name')->ignore($exceptId)]
        ];
    }
}
