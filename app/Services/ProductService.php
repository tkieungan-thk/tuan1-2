<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductAttributeService $attributeService, private ProductImageService $imageService
    ) {}

    public function create(array $data, $images, $mainIndex, $attributes): Product
    {
        return DB::transaction(function () use ($data, $images, $mainIndex, $attributes) {
            $product = Product::create($data);

            if ($images) {
                $this->imageService->processProductImages($product, $images, $mainIndex);
            }

            if ($attributes) {
                $this->attributeService->processProductAttributes($product, $attributes, false);
            }

            return $product;
        });
    }

    public function update(Product $product, array $data, ProductRequest $request): array
    {
        return DB::transaction(function () use ($product, $data, $request) {
            $product->fill($data);
            $changed = $product->isDirty();

            if ($changed) {
                $product->save();
            }

            $imgChanged = false;
            if ($request->hasFile('images')) {
                $this->imageService->processProductImages($product, $request->file('images'), $request->main_image_index);
                $imgChanged = true;
            }

            if ($request->filled('existing_main_image')) {
                $imgChanged = $this->imageService->updateMainImage($product, $request->existing_main_image);
            }

            if ($request->filled('delete_images')) {
                $imgChanged = $this->imageService->deleteProductImages($product, $request->delete_images);
            }

            $attrChanged = false;
            if ($request->has('attributes')) {
                $this->attributeService->processProductAttributes($product, $request->input('attributes'), true);
                $attrChanged = true;
            }

            return [
                'changed' => $changed || $imgChanged || $attrChanged,
                'product' => $product,
            ];
        });
    }

    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $this->imageService->deleteAllProductImages($product);
            $product->delete();
        });
    }
}
