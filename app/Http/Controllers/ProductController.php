<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\ResponseTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    use ResponseTrait;

    public function __construct(private ProductService $service) {}

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
        try {
            $product = $this->service->create(
                $request->validated(),
                $request->file('images') ?? [],
                $request->main_image_index,
                $request->input('attributes', [])
            );

            return $this->responseSuccess('products.index', __('products.created_success'));
        } catch (\Throwable $e) {
            logger()->error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->responseError(__('products.created_error'));
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
        try {
            $result = $this->service->update(
                $product,
                $request->validated(),
                $request
            );

            if ($result['changed']) {
                return $this->responseSuccess('products.show', __('products.updated_success'), $product);
            }

            return $this->responseInfo('products.show', __('products.no_changes'), $product);
        } catch (\Throwable $e) {
            return $this->responseError(__('products.updated_error'));
        }
    }

    /**
     * Xóa sản phẩm cùng ảnh và thuộc tính liên quan.
     *
     * @param  Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->service->delete($product);

            return $this->responseSuccess('products.index', __('products.deleted_success'));
        } catch (\Throwable $e) {
            return $this->responseError(__('products.deleted_error'));
        }
    }
}
