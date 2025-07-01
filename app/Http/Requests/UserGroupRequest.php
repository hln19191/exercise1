<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserGroupRequest extends FormRequest
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

        $userGroupId = $this->route('usergroup')?->id;
        
        $nameRule = Rule::unique('user_groups', 'name')->whereNull('deleted_at');
        
        // Update
        if ($userGroupId) {
            $nameRule->ignore($userGroupId, 'id');
        };

        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                $nameRule
            ],
            'is_active' => 'nullable|boolean',
        ];
              
        return $rules;
    }
}
