<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;
        $emailRule =Rule::unique('users', 'email')->whereNull('deleted_at');
        $change_password = $this->boolean('change_password');

        $passwordRule = 'required';

        // Update
        if ($userId) {
            $emailRule->ignore($userId, 'id');
            $passwordRule = 'nullable';

            //password is mandatory if 'change password' is checked
            if($change_password)
                $passwordRule = 'required';
        }

         $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'user_group_id' => ['required', Rule::exists('user_groups', 'id')],
            'email' => [
                'required',
                'email',
                $emailRule
            ],
            'password' => [
                $passwordRule,
                'string',
                'min:6',
            ],
            'is_active' => 'nullable|boolean',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'role.required' => 'Role is required',
            'user_group_id.required' => 'User group is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ];
    }
    
    public function attributes()
    {
        return [
            'name' => 'Name',
            'role' => 'Role',
            'user_group_id' => 'User group',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }
}
