<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('book'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'author'       => ['required', 'string', 'max:255'],
            'isbn'         => ['required', 'string', 'max:20', 'unique:books,isbn,' . $this->route('book')->id],
            'genre'        => ['required', 'string', 'max:100'],
            'year'         => ['required', 'integer', 'min:1000', 'max:' . date('Y')],
            'total_copies' => ['required', 'integer', 'min:1'],
        ];
    }
}
