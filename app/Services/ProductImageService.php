<?php

namespace App\Services;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    /**
     * Xử lý upload và lưu hình ảnh sản phẩm.
     *
     * @param  Product  $product
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     * @param  int|null  $mainImageIndex
     * @return void
     */
    public function processProductImages(Product $product, array $images, ?int $mainImageIndex = null): void
    {
        foreach ($images as $index => $image) {
            if ($image->isValid()) {
                $path = $image->store(Product::IMAGE_STORAGE_PATH, Product::IMAGE_DISK);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_main'    => $index === $mainImageIndex,
                ]);
            }
        }

        if (is_null($mainImageIndex) && $product->images()->count() > 0) {
            $product->images()->first()->update(['is_main' => true]);
        }
    }

    /**
     * Xử lý cập nhật ảnh khi cập nhật sản phẩm
     *
     * @param  ProductRequest  $request
     * @param  Product $product
     * @return bool
     */
    public function handleImageUpdates(Product $product, ProductRequest $request): bool
    {
        $hasImageChanges = false;

        if ($request->hasFile('images')) {
            $this->processProductImages($product, $request->file('images'), $request->main_image_index);
            $hasImageChanges = true;
        }

        if ($request->filled('existing_main_image')) {
            if ($this->updateMainImage($product, $request->existing_main_image)) {
                $hasImageChanges = true;
            }
        }

        if ($request->filled('delete_images')) {
            if ($this->deleteProductImages($product, $request->delete_images)) {
                $hasImageChanges = true;
            }
        }

        return $hasImageChanges;
    }

    /**
     * Cập nhật ảnh chính từ các ảnh hiện có.
     *
     * @param  Product  $product
     * @param  int  $imageId
     * @return bool
     */
    public function updateMainImage(Product $product, int $imageId): bool
    {
        $currentMainImage = $product->images()->where('is_main', true)->first();

        if ($currentMainImage && $currentMainImage->id == $imageId) {
            return false;
        }

        $product->images()->update(['is_main' => false]);
        $product->images()->where('id', $imageId)->update(['is_main' => true]);

        return true;
    }

    /**
     * Xóa ảnh sản phẩm
     *
     * @param  Product  $product
     * @param  array<int,int>  $imageIds
     * @return bool
     */
    public function deleteProductImages(Product $product, array $imageIds): bool
    {
        if (empty($imageIds)) {
            return false;
        }

        $imagesToDelete = $product->images()->whereIn('id', $imageIds)->get();

        if ($imagesToDelete->isEmpty()) {
            return false;
        }

        $imagePaths = $imagesToDelete->pluck('image_path')->filter()->toArray();

        if (! empty($imagePaths)) {
            Storage::disk(Product::IMAGE_DISK)->delete($imagePaths);
        }

        $wasMainImageDeleted = $imagesToDelete->contains('is_main', true);

        $product->images()->whereIn('id', $imageIds)->delete();

        if ($wasMainImageDeleted && $product->images()->exists()) {
            $product->images()->first()->update(['is_main' => true]);
        }

        return true;
    }

    /**
     * Xóa tất cả ảnh của sản phẩm khi xóa sản phẩm
     *
     * @param Product $product
     * @return void
     */
    public function deleteAllProductImages(Product $product): void
    {
        if ($product->images->isEmpty()) {
            return;
        }

        $imagePaths = $product->images->pluck('image_path')->filter()->toArray();

        if (! empty($imagePaths)) {
            Storage::disk(Product::IMAGE_DISK)->delete($imagePaths);
        }

        $product->images()->delete();
    }
}
