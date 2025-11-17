<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Khởi tạo ProductService.
     *
     * @param  ProductAttributeService  $attributeService
     * @param  ProductImageService      $imageService
     */
    public function __construct(private ProductAttributeService $attributeService, private ProductImageService $imageService
    ) {}

    /**
     * Tạo sản phẩm
     *
     * @param array $data
     * @param mixed $images
     * @param mixed $mainIndex
     * @param mixed $attributes
     * @return Product
     */
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

    /**
     * Cập nhật sản phẩm
     *
     * @param \App\Models\Product $product
     * @param array $data
     * @param \App\Http\Requests\ProductRequest $request
     * @return array
     */
    public function update(Product $product, array $data, ProductRequest $request): array
    {
        return DB::transaction(function () use ($product, $data, $request) {
            $product->fill($data);
            $changed = $product->isDirty();

            if ($changed) {
                $product->save();
            }
            
            $imgChanged = $this->imageService->handleImageUpdates($product, $request);

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

    /**
     * Xóa sản phẩm + ảnh
     *
     * @param Product $product
     * @return void
     */
    public function delete(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $this->imageService->deleteAllProductImages($product);
            $product->delete();
        });
    }
}
