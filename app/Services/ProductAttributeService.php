<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;

class ProductAttributeService
{
    /**
     * Xử lý lưu attributes và values.
     *
     * @param  Product  $product
     * @param  array<int, array{name: string, values: array<int,string>|string}>  $attributes
     * @return void
     */
    public function processProductAttributes(Product $product, array $attributes, bool $isUpdate = false): void
    {
        $existingAttributeIds = [];

        foreach ($attributes as $attributeData) {
            if (empty($attributeData['name']) || empty($attributeData['values'])) {
                continue;
            }

            $attribute = Attribute::updateOrCreate(
                [
                    'id'         => $isUpdate ? ($attributeData['id'] ?? null) : null,
                    'product_id' => $product->id,
                ],
                [
                    'name' => trim($attributeData['name']),
                ]
            );

            $existingAttributeIds[] = $attribute->id;

            $values = $this->parseAttributeValues($attributeData['values']);
            $this->syncAttributeValues($attribute, $values);
        }

        if ($isUpdate && ! empty($existingAttributeIds)) {
            Attribute::where('product_id', $product->id)
                ->whereNotIn('id', $existingAttributeIds)
                ->delete();
        }
    }

    /**
     * Đồng bộ values cho attribute
     *
     * @param Attribute
     * @param array
     */
    public function syncAttributeValues(Attribute $attribute, array $newValues): void
    {
        $normalizedValues = array_unique(array_map('trim', $newValues));

        $attribute->values()->delete();

        foreach ($normalizedValues as $value) {
            if (! empty($value)) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value'        => $value,
                ]);
            }
        }
    }

    /**
     * Parse values
     *
     * @param $values
     * @param array
     */
    public function parseAttributeValues(mixed $values): array
    {
        if (is_array($values)) {
            return $values;
        }

        if (is_string($values)) {
            return array_map('trim', explode(',', $values));
        }

        return [];
    }
}
