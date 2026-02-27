<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'address'    => ['required', 'string', 'max:255'],
            'zip'        => ['required', 'string', 'max:20'],
            'town'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:50'],
            'email'      => ['required', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'role'       => ['sometimes', 'in:member,librarian'],
        ];
    }
}