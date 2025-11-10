@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
            <div>
                @if($product->mainImage)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $product->mainImage->image_path) }}" 
                             alt="{{ $product->name }}" class="w-full rounded-lg">
                    </div>
                @endif

                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-20 h-20 object-cover rounded border-2 {{ $image->is_main ? 'border-blue-500' : 'border-gray-200' }}">
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
                
                <p class="text-gray-600 mb-4">
                    Danh mục: <span class="font-semibold">{{ $product->category->name }}</span>
                </p>

                <div class="text-3xl font-bold text-red-600 mb-6">
                    {{ $product->formatted_price }}
                </div>

                @if($product->attributes->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Thông số kỹ thuật</h3>
                        @foreach($product->attributes as $attribute)
                            <div class="flex border-b border-gray-200 py-2">
                                <span class="font-medium w-1/3">{{ $attribute->name }}:</span>
                                <span class="w-2/3">
                                    {{ $attribute->values->pluck('value')->implode(', ') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mb-6">
                    <span class="text-lg {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock > 0 ? '✓ Còn hàng' : '✗ Hết hàng' }}
                    </span>
                    <span class="text-gray-600 ml-2">({{ $product->stock }} sản phẩm)</span>
                </div>

                <div class="flex space-x-4">
                    <button class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Thêm vào giỏ hàng
                    </button>
                    <button class="flex-1 bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold">
                        Mua ngay
                    </button>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 p-8">
            <h2 class="text-2xl font-bold mb-4">Mô tả sản phẩm</h2>
            <div class="prose max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    </div>

    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection