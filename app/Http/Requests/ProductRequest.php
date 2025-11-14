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
     * @return array|array{category_id: string, max_price: string, min_price: string, search: string, status: string|array{email: array<string|\Illuminate\Validation\Rules\Unique>, name: string[], password: string[]}}
     */
    public function rules(): array
    {
        if ($this->isMethod('GET')) {
            return $this->getRulesForGet();
        }
        if ($this->isMethod('POST')) {
            return $this->getRulesForCreate();
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            return $this->getRulesForUpdate();
        }

        return [];
    }

    /**
     * Trả về validate khi lọc, tìm kiếm sản phẩm.
     *
     * @return array{category_id: string, max_price: string, min_price: string, search: string, status: string}
     */
    private function getRulesForGet(): array
    {
        return [
            'search'      => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'min_price'   => ['nullable', 'numeric', 'min:0'],
            'max_price'   => ['nullable', 'numeric', 'min:0'],
            'status'      => ['nullable', 'in:active,inactive,draft'],
        ];
    }

    /**
     * Trả về validate khi tạo mới sản phẩm.
     *
     * @return array{attributes.*.name: string, attributes.*.values: string, attributes.*.values.*: string, category_id: string, description: string, images.*: string, main_image_index: string, name: string, price: string, status: string, stock: string}
     */
    private function getRulesForCreate(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'category_id'           => ['required', 'exists:categories,id'],
            'description'           => ['nullable', 'string'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'stock'                 => ['required', 'integer', 'min:0'],
            'status'                => ['sometimes', 'in:active,inactive,draft'],
            'images.*'              => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'main_image_index'      => ['nullable', 'integer', 'min:0'],
            'attributes.*.name'     => ['sometimes', 'required', 'string', 'max:255'],
            'attributes.*.values'   => ['sometimes', 'required'],
            'attributes.*.values.*' => ['string', 'max:255'],
        ];
    }

    /**
     * Trả về validate khi cập nhật sản phẩm.
     *
     * @return array{attributes.*.id: string, attributes.*.name: string, attributes.*.values: string, attributes.*.values.*: string, category_id: string, delete_images.*: string, description: string, existing_main_image: string, images.*: string, main_image_index: string, name: string, price: string, status: string, stock: string}
     */
    private function getRulesForUpdate(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'category_id'           => ['required', 'exists:categories,id'],
            'description'           => ['nullable', 'string'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'stock'                 => ['required', 'integer', 'min:0'],
            'status'                => ['sometimes', 'in:active,inactive,draft'],
            'images.*'              => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'main_image_index'      => ['nullable', 'integer', 'min:0'],
            'existing_main_image'   => ['nullable', 'exists:product_images,id'],
            'delete_images.*'       => ['nullable', 'exists:product_images,id'],
            'attributes.*.id'       => ['sometimes', 'exists:attributes,id'],
            'attributes.*.name'     => ['sometimes', 'required', 'string', 'max:255'],
            'attributes.*.values'   => ['sometimes', 'required'],
            'attributes.*.values.*' => ['string', 'max:255'],
        ];
    }
}
