@extends('layouts.app')

@section('title', __('products.item_update: ') . $product->name)

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('products.page_title') }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('products.item1') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('products.item_update') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <form action="{{ route('products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <h5 class="fw-semibold mb-3">{{ __('products.information') }} </h5>
                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">{{ __('products.name') }} *</label>
                                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">{{ __('products.category') }} *</label>
                                    <select name="category_id"
                                        class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">{{ __('products.choose_category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ __('products.price') }} *</label>
                                    <input type="number" name="price" value="{{ old('price', $product->price) }}"
                                        class="form-control @error('price') is-invalid @enderror" min="0"
                                        step="1000" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ __('products.stock') }} *</label>
                                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                                        class="form-control @error('stock') is-invalid @enderror" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ __('products.status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="active"
                                            {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>{{ __('products.status_active') }}
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>{{ __('products.status_inactive') }}
                                        </option>
                                        <option value="draft"
                                            {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>{{ __('products.status_draft') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <h5 class="fw-semibold mb-3">{{ __('products.images') }}</h5>

                            @if ($product->images->count() > 0)
                                <label class="form-label">Ảnh hiện tại</label>
                                <div class="row mb-4">
                                    @foreach ($product->images as $image)
                                        <div class="col-md-3 mb-3">
                                            <div class="position-relative border rounded p-1">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                    alt="{{ $product->name }}" class="img-fluid rounded mb-2"
                                                    style="height: 140px; object-fit: cover;">
                                                <div class="form-check">
                                                    <input type="radio" name="existing_main_image"
                                                        value="{{ $image->id }}" class="form-check-input"
                                                        {{ $image->is_main ? 'checked' : '' }}>
                                                    <label class="form-check-label small">{{ __('products.image_main') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" name="delete_images[]"
                                                        value="{{ $image->id }}" class="form-check-input">
                                                    <label class="form-check-label small text-danger">{{ __('products.delete_image') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mb-4">
                                <label class="form-label">{{ __('products.add_new_image') }}</label>
                                <input type="file" name="images[]" id="images" multiple accept="image/*"
                                    class="form-control @error('images.*') is-invalid @enderror">
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('products.image_hint') }}</small>
                            </div>

                            <div id="image-preview" class="row g-3 d-none"></div>

                            <h5 class="fw-semibold mb-3 mt-4">{{ __('products.attributes') }}</h5>
                            <div id="attributes-container">
                                @forelse($product->attributes as $index => $attribute)
                                    <div class="border rounded p-3 mb-3 bg-light attribute-group">
                                        <input type="hidden" name="attributes[{{ $index }}][id]"
                                            value="{{ $attribute->id }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('products.attribute_name') }}</label>
                                                <input type="text" name="attributes[{{ $index }}][name]"
                                                    value="{{ old('attributes.' . $index . '.name', $attribute->name) }}"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('products.attribute_value') }}</label>
                                                <input type="text" name="attributes[{{ $index }}][values]"
                                                    value="{{ old('attributes.' . $index . '.values', $attribute->values->pluck('value')->implode(', ')) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-link text-danger p-0 mt-2 remove-attribute">✕ {{ __('products.remove_attribute') }}</button>
                                    </div>
                                @empty
                                    <div class="border rounded p-3 mb-3 bg-light attribute-group">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('products.attribute_name') }}</label>
                                                <input type="text" name="attributes[0][name]" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('products.attribute_value') }}</label>
                                                <input type="text" name="attributes[0][values]" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <button type="button" id="add-attribute" class="btn btn-outline-secondary btn-sm mb-4">
                                + {{ __('products.add_attribute') }}
                            </button>

                            <h5 class="fw-semibold mb-3">{{ __('products.description') }}</h5>
                            <textarea name="description" rows="5" class="form-control mb-4 @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('products.show', $product->id) }}"
                                        class="btn btn-secondary">{{ __('products.btn_cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('products.btn_save') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const translations = {
                mustHaveOneAttribute: "{{ __('products.must_have_one_attribute') }}",
            };
            const imageInput = document.getElementById('images');
            const imagePreview = document.getElementById('image-preview');

            imageInput.addEventListener('change', function() {
                imagePreview.innerHTML = '';
                if (this.files.length > 0) {
                    imagePreview.classList.remove('d-none');
                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = e => {
                            const col = document.createElement('div');
                            col.className = 'col-md-3';
                            col.innerHTML =
                                `<img src="${e.target.result}" class="img-fluid rounded border" style="height:140px; object-fit:cover;">`;
                            imagePreview.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    imagePreview.classList.add('d-none');
                }
            });

            let attributeCount = {{ $product->attributes->count() }};
            const container = document.getElementById('attributes-container');
            document.getElementById('add-attribute').addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 bg-light attribute-group';
                div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('products.attribute_name') }}</label>
                    <input type="text" name="attributes[${attributeCount}][name]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('products.attribute_values') }}</label>
                    <input type="text" name="attributes[${attributeCount}][values]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-link text-danger p-0 mt-2 remove-attribute">✕ {{ __('products.remove_attribute') }}</button>
        `;
                container.appendChild(div);
                attributeCount++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-attribute')) {
                    if (document.querySelectorAll('.attribute-group').length > 1) {
                        e.target.closest('.attribute-group').remove();
                    } else {
                        alert(translations.mustHaveOneAttribute);
                    }
                }
            });
        });
    </script>
@endsection
