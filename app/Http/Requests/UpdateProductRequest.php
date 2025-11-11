<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $rules = [
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'status'      => 'sometimes|in:active,inactive,draft',

            'images.*'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'main_image_index'    => 'nullable|integer|min:0',
            'existing_main_image' => 'nullable|exists:product_images,id',
            'delete_images.*'     => 'nullable|exists:product_images,id',

            'attributes.*.id'       => 'sometimes|exists:attributes,id',
            'attributes.*.name'     => 'sometimes|required|string|max:255',
            'attributes.*.values'   => 'sometimes|required',
            'attributes.*.values.*' => 'string|max:255',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['images.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }
}
