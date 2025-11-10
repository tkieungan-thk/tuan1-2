<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm với phân trang và filter
     * 
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

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($id): View
    {
        $product = Product::with([
            'category',
            'images',
            'attributes.values'
        ])->findOrFail($id);

        // Lấy sản phẩm liên quan cùng category
        $relatedProducts = Product::with(['images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * API trả về JSON danh sách sản phẩm
     */
    public function apiIndex(Request $request)
    {
        $products = Product::with(['category', 'images', 'attributes.values'])
            ->where('status', 'active')
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Danh sách sản phẩm'
        ]);
    }

    /**
     * Hiển thị sản phẩm theo category
     */
    public function byCategory($categoryId): View
    {
        $category = Category::findOrFail($categoryId);
        $products = Product::with(['category', 'images', 'attributes.values'])
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->paginate(12);

        return view('products.by_category', compact('products', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
