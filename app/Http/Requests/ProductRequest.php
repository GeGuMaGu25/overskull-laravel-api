<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El campo nombre es requerido',
            'name.string' => 'El nombre debe ser un texto',
            'name.max' => 'El nombre no puede superar los 150 caracteres',
            'description.string' => 'La descripción debe ser un texto',
            'description.max' => 'La descripción no puede superar los 1000 caracteres',
            'price.required' => 'El campo precio es requerido',
            'price.numeric' => 'El precio debe ser numérico',
            'price.gt' => 'El precio debe ser mayor que 0',
            'price.max' => 'El precio excede el valor máximo permitido',
            'stock.required' => 'El campo stock es requerido',
            'stock.integer' => 'El stock debe ser un número entero',
            'stock.min' => 'El stock no puede ser negativo',
            'category_id.required' => 'La categoría es requerida',
            'category_id.integer' => 'La categoría no es válida',
            'category_id.exists' => 'La categoría seleccionada no existe',
        ];
    }

    /**
     * Normalize input before validation.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge(['name' => trim($this->input('name'))]);
        }
    }
}
