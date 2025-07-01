<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('role') ?->id;

        $nameRule = Rule::unique('roles', 'name')->whereNull('deleted_at');
        
        if($id){
            // If the role is being updated, we need to ignore the current role's ID for unique validation
            $nameRule->ignore($id, 'id');
        }
        
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                $nameRule
            ],
            'permissions' => [
                'array'
            ],
            'permissions.*' => [Rule::exists('permissions', 'name')],
        ];

        return $rules;
    }
}
