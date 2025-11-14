<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     *
     * @param  ProductRequest  $request
     * @return View
     */
    public function index(ProductRequest $request): View
    {
        $safe = $request->safe()->only(['search', 'category_id', 'min_price', 'max_price', 'status']);

        $products = Product::query()
            ->filter($safe)
            ->with(['category', 'images', 'attributes.values'])
            ->latest('id')
            ->get();

        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết sản phẩm.
     *
     * @param  Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $product->loadMissing(['category', 'images', 'attributes.values']);

        return view('products.show', compact('product'));
    }

    /**
     * Hiện form tạo sản phẩm mới
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Xử lý lưu sản phẩm.
     *
     * @param  ProductRequest  $request
     * @return RedirectResponse
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status ?? Product::DEFAULT_STATUS->value,
            ]);

            if ($request->hasFile('images')) {
                $this->processProductImages($product, $request->file('images'), $request->main_image_index);
            }

            if ($request->has('attributes')) {
                $this->processProductAttributes($product, $request->input('attributes'), false);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', __('products.created_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', __('products.created_error') . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm.
     *
     * @param  Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $product->loadMissing(['category', 'images', 'attributes.values']);
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật thông tin sản phẩm.
     *
     * @param  ProductRequest  $request
     * @param  Product $product
     * @return RedirectResponse
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product->fill([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'status' => $request->status ?? Product::DEFAULT_STATUS->value,
            ]);

            $hasChanges = $product->isDirty();

            if ($hasChanges) {
                $product->save();
            }

            $imageChanges = $this->handleImageUpdates($product, $request);

            $attributeChanges = false;
            if ($request->has('attributes')) {
                $this->processProductAttributes($product, $request->input('attributes'), true);
                $attributeChanges = true;
            }

            if ($hasChanges || $imageChanges || $attributeChanges) {
                DB::commit();

                return redirect()->route('products.show', $product)
                    ->with('success', __('products.updated_success'));
            } else {
                DB::rollBack();

                return redirect()->route('products.show', $product)
                    ->with('info', __('products.no_changes'));
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', __('products.updated_error'));
        }
    }

    /**
     * Xóa sản phẩm cùng ảnh và thuộc tính liên quan.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product->load(['images']);
            $this->deleteAllProductImages($product);
            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', __('products.deleted_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('products.deleted_error') . $e->getMessage());
        }
    }

    /**
     * Xử lý upload và lưu hình ảnh sản phẩm.
     *
     * @param  Product  $product
     * @param  array<int, \Illuminate\Http\UploadedFile>  $images
     * @param  int|null  $mainImageIndex
     * @return void
     */
    private function processProductImages(Product $product, array $images, ?int $mainImageIndex = null): void
    {
        foreach ($images as $index => $image) {
            if ($image->isValid()) {
                $path = $image->store(Product::IMAGE_STORAGE_PATH, Product::IMAGE_DISK);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_main' => $index === $mainImageIndex,
                ]);
            }
        }

        if (is_null($mainImageIndex) && $product->images()->count() > 0) {
            $product->images()->first()->update(['is_main' => true]);
        }
    }

    /**
     * Xử lý lưu attributes và values.
     *
     * @param  Product  $product
     * @param  array<int, array{name: string, values: array<int,string>|string}>  $attributes
     * @return void
     */
    private function processProductAttributes(Product $product, array $attributes, bool $isUpdate = false): void
    {
        $existingAttributeIds = [];

        foreach ($attributes as $attributeData) {
            if (empty($attributeData['name']) || empty($attributeData['values'])) {
                continue;
            }

            $attribute = Attribute::updateOrCreate(
                [
                    'id' => $isUpdate ? ($attributeData['id'] ?? null) : null,
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

        if ($isUpdate && !empty($existingAttributeIds)) {
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
    private function syncAttributeValues(Attribute $attribute, array $newValues): void
    {
        $normalizedValues = array_unique(array_map('trim', $newValues));

        $attribute->values()->delete();

        foreach ($normalizedValues as $value) {
            if (!empty($value)) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value,
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
    private function parseAttributeValues(mixed $values): array
    {
        if (is_array($values)) {
            return $values;
        }

        if (is_string($values)) {
            return array_map('trim', explode(',', $values));
        }

        return [];
    }

    /**
     * Xử lý cập nhật ảnh khi cập nhật sản phẩm
     *
     * @param  ProductRequest  $request
     * @param  Product $product
     * @return bool
     */
    private function handleImageUpdates(Product $product, ProductRequest $request): bool
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
    private function updateMainImage(Product $product, int $imageId): bool
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
    private function deleteProductImages(Product $product, array $imageIds): bool
    {
        if (empty($imageIds)) {
            return false;
        }

        $imagesToDelete = $product->images()->whereIn('id', $imageIds)->get();

        if ($imagesToDelete->isEmpty()) {
            return false;
        }

        $imagePaths = $imagesToDelete->pluck('image_path')->filter()->toArray();

        if (!empty($imagePaths)) {
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
    private function deleteAllProductImages(Product $product): void
    {
        if ($product->images->isEmpty()) {
            return;
        }

        $imagePaths = $product->images->pluck('image_path')->filter()->toArray();

        if (!empty($imagePaths)) {
            Storage::disk(Product::IMAGE_DISK)->delete($imagePaths);
        }

        $product->images()->delete();
    }
}