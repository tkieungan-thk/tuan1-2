@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('products.page_title') }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('products.item1') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('products.item_detail') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif


        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if ($product->mainImage)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $product->mainImage->image_path) }}"
                                            alt="{{ $product->name }}" class="img-fluid rounded border shadow-sm">
                                    </div>
                                @endif

                                @if ($product->images->count() > 1)
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        @foreach ($product->images as $image)
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                alt="{{ $product->name }}"
                                                class="rounded border {{ $image->is_main ? 'border-primary' : 'border-secondary' }}"
                                                style="width:80px; height:80px; object-fit:cover;">
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h2 class="fw-bold mb-3">{{ $product->name }}</h2>
                                <p class="text-muted mb-2">
                                    <strong>{{ __('products.category') }}: </strong> {{ $product->category->name }}
                                </p>

                                <h3 class="text-danger fw-bold mb-4">{{ $product->formatted_price }}</h3>

                                @if ($product->attributes->count() > 0)
                                    <h5 class="fw-semibold mb-2">{{ __('products.information') }}</h5>
                                    <table class="table table-sm table-bordered mb-4">
                                        <tbody>
                                            @foreach ($product->attributes as $attribute)
                                                <tr>
                                                    <th style="width: 40%">{{ $attribute->name }}</th>
                                                    <td>{{ $attribute->values->pluck('value')->implode(', ') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif

                                <div class="mb-4">
                                    <span class="fw-bold {{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $product->stock > 0 ? '✓ ' . __('products.stock_0') : '✗ ' . __('products.stock_0') }}
                                    </span>
                                    <span class="text-muted ms-2">({{ $product->stock }} {{ __('products.name') }})</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="fw-bold mb-3">{{ __('products.description') }}</h4>
                        <div class="border p-3 rounded bg-light">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
