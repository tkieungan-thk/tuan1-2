<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images', 'attributes.values'])
            ->where('status', 'active');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products   = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết sản phẩm theo ID.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id): View
    {
        $product = Product::with([
            'category',
            'images',
            'attributes.values',
        ])->findOrFail($id);

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
     * Lưu sản phẩm vào database.
     *
     * @param  CreateProductRequest  $request
     * @return RedirectResponse
     */
    public function store(CreateProductRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'status'      => $request->status ?? 'active',
            ]);

            if ($request->hasFile('images')) {
                $this->processProductImages($product, $request->file('images'), $request->main_image_index);
            }

            if ($request->has('attributes')) {
                $this->processProductAttributes($product, $request->input('attributes'));
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
                $path = $image->store('products', 'public');

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
     * Xử lý lưu attributes và values.
     *
     * @param  Product  $product
     * @param  array<int, array{name: string, values: array<int,string>|string}>  $attributes
     * @return void
     */
    private function processProductAttributes(Product $product, array $attributes): void
    {
        foreach ($attributes as $attributeData) {
            if (! empty($attributeData['name']) && ! empty($attributeData['values'])) {
                $attribute = Attribute::create([
                    'name'       => $attributeData['name'],
                    'product_id' => $product->id,
                ]);

                $values = is_array($attributeData['values'])
                    ? $attributeData['values']
                    : explode(',', $attributeData['values']);

                foreach ($values as $value) {
                    if (! empty(trim($value))) {
                        AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value'        => trim($value),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id): View
    {
        $product = Product::with(['category', 'images', 'attributes.values'])
            ->findOrFail($id);
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Cập nhật thông tin sản phẩm.
     *
     * @param  UpdateProductRequest  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, $id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            $product->update([
                'name'        => $request->name,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'status'      => $request->status ?? 'active',
            ]);

            if ($request->hasFile('images')) {
                $this->processProductImages($product, $request->file('images'), $request->main_image_index);
            }

            if ($request->has('existing_main_image')) {
                $this->updateMainImage($product, $request->existing_main_image);
            }

            if ($request->has('delete_images')) {
                $this->deleteProductImages($product, $request->delete_images);
            }

            $this->syncProductAttributes($product, $request->input('attributes', []));

            DB::commit();

            return redirect()->route('products.show', $product->id)
                ->with('success', __('products.updated_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', __('products.updated_error') . $e->getMessage());
        }
    }

    /**
     * Cập nhật ảnh chính từ các ảnh hiện có.
     *
     * @param  Product  $product
     * @param  int  $imageId
     * @return void
     */
    private function updateMainImage(Product $product, int $imageId): void
    {
        $product->images()->update(['is_main' => false]);

        $product->images()->where('id', $imageId)->update(['is_main' => true]);
    }

    /**
     * Xóa ảnh sản phẩm theo ID.
     *
     * @param  Product  $product
     * @param  array<int,int>  $imageIds
     * @return void
     */
    private function deleteProductImages(Product $product, array $imageIds): void
    {
        $imagesToDelete = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        if ($product->images()->count() > 0 && ! $product->images()->where('is_main', true)->exists()) {
            $product->images()->first()->update(['is_main' => true]);
        }
    }

    /**
     * Đồng bộ lại attributes của sản phẩm (thêm, sửa, xóa).
     *
     * @param  Product  $product
     * @param  array<int, array{id?: int, name: string, values: array<int,string>|string}>  $newAttributes
     * @return void
     */
    private function syncProductAttributes(Product $product, array $newAttributes): void
    {
        $existingAttributeIds = [];

        foreach ($newAttributes as $attributeData) {
            if (! empty($attributeData['name']) && ! empty($attributeData['values'])) {
                $attribute = Attribute::updateOrCreate(
                    [
                        'id'         => $attributeData['id'] ?? null,
                        'product_id' => $product->id,
                    ],
                    [
                        'name' => $attributeData['name'],
                    ]
                );

                $existingAttributeIds[] = $attribute->id;

                $attribute->values()->delete();

                $values = is_array($attributeData['values'])
                    ? $attributeData['values']
                    : array_map('trim', explode(',', $attributeData['values']));

                foreach ($values as $value) {
                    if (! empty($value)) {
                        AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value'        => $value,
                        ]);
                    }
                }
            }
        }

        Attribute::where('product_id', $product->id)
            ->whereNotIn('id', $existingAttributeIds)
            ->delete();
    }

    /**
     * Xóa sản phẩm cùng ảnh và thuộc tính liên quan.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::with(['images', 'attributes.values'])->findOrFail($id);

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', __('products.deleted_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('products.deleted_error') . $e->getMessage());
        }
    }
}
