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
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;

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
            ->paginate(Product::PAGINATION_PER_PAGE)
            ->withQueryString();

        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết sản phẩm theo ID.
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
     * Lưu sản phẩm vào database.
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
    private function processProductAttributes(Product $product, array $attributes): void
    {
        foreach ($attributes as $attributeData) {
            if (!empty($attributeData['name']) && !empty($attributeData['values'])) {
                $attribute = Attribute::create([
                    'name' => $attributeData['name'],
                    'product_id' => $product->id,
                ]);

                $values = is_array($attributeData['values'])
                    ? $attributeData['values']
                    : explode(',', $attributeData['values']);

                foreach ($values as $value) {
                    if (!empty(trim($value))) {
                        AttributeValue::create([
                            'attribute_id' => $attribute->id,
                            'value' => trim($value),
                        ]);
                    }
                }
            }
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

            $hasChanges = false;

            if ($product->isDirty()) {
                $product->save();
                $hasChanges = true;
            }

            $imageChanges = $this->handleImageUpdates($product, $request);

            $attributeChanges = $this->handleAttributeUpdates($product, $request);

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
            $this->processProductImages(
                $product,
                $request->file('images'),
                $request->main_image_index
            );
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
     * @return void
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
     * Xử lý khi thuộc tính có thay đổi
     *
     * @param  Product  $product
     * @param  array<int, array{id?: int, name: string, values: array<int,string>|string}>  $newAttributes
     * @return void
     */
    private function handleAttributeUpdates(Product $product, ProductRequest $request): bool
    {
        $attributes = $request->input('attributes', []);

        if (empty($attributes) || collect($attributes)->filter()->isEmpty()) {
            return false;
        }

        $this->syncProductAttributes($product, $attributes);

        return true;
    }

    /**
     * Đồng bộ attributes của sản phẩm
     *
     */
    private function syncProductAttributes(Product $product, array $newAttributes): void
    {
        $existingAttributeIds = [];

        foreach ($newAttributes as $attributeData) {
            if (empty($attributeData['name']) || empty($attributeData['values'])) {
                continue;
            }

            $attribute = Attribute::updateOrCreate(
                [
                    'id' => $attributeData['id'] ?? null,
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

        if (!empty($existingAttributeIds)) {
            Attribute::where('product_id', $product->id)
                ->whereNotIn('id', $existingAttributeIds)
                ->delete();
        }
    }

    /**
     * Đồng bộ values cho attribute
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

    //============= IM/EX
    public function export(): BinaryFileResponse
    {
        $fileName = 'products_' . date('Y_m_d_His') . '.xlsx';

        return Excel::download(new ProductsExport, $fileName);
    }

    /**
     * Import sản phẩm từ Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        try {
            $import = new ProductsImport;

            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $errors = $import->getErrors();

            $message = "Import thành công {$successCount} sản phẩm.";

            if (!empty($errors)) {
                $message .= " Có " . count($errors) . " lỗi: " . implode('; ', array_slice($errors, 0, 5));

                if (count($errors) > 5) {
                    $message .= "... và " . (count($errors) - 5) . " lỗi khác.";
                }
            }

            $type = empty($errors) ? 'success' : 'warning';

            return redirect()->route('products.index')
                ->with($type, $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Import thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Download template import
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $templatePath = storage_path('app/templates/product_import_template.xlsx');

        // Tạo template nếu chưa có
        if (!file_exists($templatePath)) {
            $this->createImportTemplate();
        }

        return response()->download($templatePath, 'product_import_template.xlsx');
    }

    /**
     * Tạo template import
     */
    private function createImportTemplate(): void
    {
        $headers = [
            'Tên sản phẩm',
            'Danh mục',
            'Mô tả',
            'Giá (VND)',
            'Tồn kho',
            'Trạng thái',
            'Thuộc tính'
        ];

        $examples = [
            [
                'iPhone 15 Pro',
                'Điện thoại',
                'iPhone 15 Pro 128GB',
                '25.000.000',
                '50',
                'Hoạt động',
                'Màu sắc: Xanh, Đen; Bộ nhớ: 128GB, 256GB'
            ],
            [
                'Samsung Galaxy S24',
                'Điện thoại',
                'Samsung Galaxy S24 Ultra',
                '22.000.000',
                '30',
                'Hoạt động',
                'Màu sắc: Trắng, Tím; Bộ nhớ: 256GB, 512GB'
            ]
        ];

        $export = new class ($headers, $examples) implements FromArray {
            private $headers;
            private $examples;

            public function __construct($headers, $examples)
            {
                $this->headers = $headers;
                $this->examples = $examples;
            }

            public function array(): array
            {
                return [
                    $this->headers,
                    ...$this->examples
                ];
            }
        };

        Excel::store($export, 'templates/product_import_template.xlsx');
    }
}