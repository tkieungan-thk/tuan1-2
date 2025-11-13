@extends('layouts.app')

@section('title', __('products.item_create'))

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('products.item_create') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30">
                    <div class="card-body">
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <h5 class="mb-3 text-primary">{{ __('products.information') }}</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">{{ __('products.name') }} *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">{{ __('products.category') }} *</label>
                                    <select name="category_id" id="category_id"
                                        class="form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">-- {{ __('products.opt_category') }} --</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="price" class="form-label">{{ __('products.price') }} *</label>
                                    <input type="number" name="price" id="price" value="{{ old('price') }}"
                                        class="form-control @error('price') is-invalid @enderror" min="0"
                                        step="1000" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="stock" class="form-label">{{ __('products.stock') }} *</label>
                                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}"
                                        class="form-control @error('stock') is-invalid @enderror" min="0" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">{{ __('products.status') }}</label>
                                    <select name="status" id="status" class="form-control">
                                        @foreach (__('products.status_enum') as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('status', 'active') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <h5 class="mb-3 mt-4 text-primary">{{ __('products.images') }}</h5>
                            <div class="mb-3">
                                <label class="form-label">{{ __('products.choose_images') }}</label>
                                <input type="file" name="images[]" id="images" multiple accept="image/*"
                                    class="form-control @error('images.*') is-invalid @enderror">
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('products.image_hint') }}</small>
                            </div>

                            <div id="image-preview" class="row mb-3 d-none"></div>

                            <h5 class="mb-3 mt-4 text-primary">{{ __('products.attributes') }}</h5>
                            <div id="attributes-container">
                                <div class="border rounded p-3 mb-3 attribute-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">{{ __('products.attribute_name') }} *</label>
                                            <input type="text" name="attributes[0][name]" class="form-control"
                                                placeholder="{{ __('products.attribute_name_example') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">{{ __('products.attribute_values') }} *</label>
                                            <input type="text" name="attributes[0][values]" class="form-control"
                                                placeholder="{{ __('products.attribute_value_example') }}" required>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-attribute mt-2">
                                        <i class="fa fa-times"></i> {{ __('products.remove_attribute') }}
                                    </button>
                                </div>
                            </div>
                            <button type="button" id="add-attribute" class="btn btn-outline-secondary btn-sm mb-4">
                                <i class="fa fa-plus"></i> {{ __('products.add_attribute') }}
                            </button>

                            <h5 class="mb-3 text-primary">{{ __('products.description') }}</h5>
                            <div class="mb-3">
                                <textarea name="description" id="description" rows="6"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="{{ __('products.description_placeholder') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-start">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary me-2 mr-2">
                                    {{ __('products.btn_cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('products.btn_save') }}
                                </button>
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
            const attributesContainer = document.getElementById('attributes-container');
            const addBtn = document.getElementById('add-attribute');
            let attrCount = 1;

            let selectedFiles = [];

            imageInput.addEventListener('change', function() {
                selectedFiles = Array.from(this.files);
                renderPreviews();
            });

            function renderPreviews() {
                imagePreview.innerHTML = '';
                if (selectedFiles.length > 0) {
                    imagePreview.classList.remove('d-none');
                    selectedFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-3 position-relative mb-3';

                            col.innerHTML = `
                                <div class="border rounded overflow-hidden shadow-sm">
                                    <img src="${e.target.result}" class="img-fluid" style="height:200px;object-fit:cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle"
                                        onclick="removeImage(${index})" title="Xóa ảnh">
                                        &times;
                                    </button>
                                </div>
                            `;
                            imagePreview.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    });
                } else {
                    imagePreview.classList.add('d-none');
                }
                updateFileInput();
            }

            function removeImage(index) {
                selectedFiles.splice(index, 1);
                renderPreviews();
            }

            window.removeImage = removeImage;

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;
            }

            // imageInput.addEventListener('change', function() {
            //     imagePreview.innerHTML = '';
            //     if (this.files.length > 0) {
            //         imagePreview.classList.remove('d-none');
            //         Array.from(this.files).forEach(file => {
            //             const reader = new FileReader();
            //             reader.onload = function(e) {
            //                 const col = document.createElement('div');
            //                 col.className = 'col-md-3 mb-2 rounded mr-2';
            //                 col.innerHTML =
            //                     `<img src="${e.target.result}" class="img-fluid rounded border">`;
            //                 imagePreview.appendChild(col);
            //             };
            //             reader.readAsDataURL(file);
            //         });
            //     } else {
            //         imagePreview.classList.add('d-none');
            //     }
            // });

            addBtn.addEventListener('click', function() {
                const newAttr = document.createElement('div');
                newAttr.className = 'border rounded p-3 mb-3 attribute-group';
                newAttr.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">{{ __('products.attribute_name') }}</label>
                    <input type="text" name="attributes[${attrCount}][name]" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">{{ __('products.attribute_values') }}</label>
                    <input type="text" name="attributes[${attrCount}][values]" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-attribute mt-2">
                <i class="fa fa-times"></i> {{ __('products.remove_attribute') }}
            </button>
        `;
                attributesContainer.appendChild(newAttr);
                attrCount++;
            });

            attributesContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-attribute')) {
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
