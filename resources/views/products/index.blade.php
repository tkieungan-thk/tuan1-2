@extends('layouts.app')

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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('products.item2') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
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
                        <div class="mb-3">
                            <form action="{{ route('products.index') }}" method="GET" class="align-items-center row g-2">

                                <div class="col-md-3">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="{{ __('products.search_product') }}">
                                </div>

                                <div class="col-md-3">
                                    <select name="category_id" class="form-control">
                                        <option value="">{{ __('products.lb_category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                        class="form-control" placeholder="{{ __('products.min_price') }}">
                                </div>

                                <div class="col-md-2">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        class="form-control" placeholder="{{ __('products.max_price') }}">
                                </div>

                                <div class="col-md-2 d-flex gap-1">
                                    <button type="submit" class="btn btn-primary me-2 mr-2">
                                        <i class="fa fa-search"></i> {{ __('products.filter') }}
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-undo"></i> {{ __('products.reset') }}
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table mb-0 table-hover">
                                <thead class="thead-default">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('products.name') }}</th>
                                        <th>{{ __('products.category') }}</th>
                                        <th>{{ __('products.price') }}</th>
                                        <th>{{ __('products.stock') }}</th>
                                        <th scope="col" colspan="2">{{ __('products.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>
                                                <a href="{{ route('products.show', $product->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td>{{ $product->category->name ?? '—' }}</td>
                                            <td>{{ $product->formatted_price }}</td>
                                            <td>
                                                @if ($product->isOutOfStock())
                                                    <span class="badge badge-danger">
                                                        {{ $product->stockStatus->label() }}
                                                    </span>
                                                @elseif($product->isLowStock())
                                                    <span class="badge badge-warning">
                                                        {{ $product->formatted_stock }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-success">
                                                        {{ $product->formatted_stock }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $product->status->color() }}">
                                                    {{ $product->status->label() }}
                                                </span>
                                            </td>
                                            <td class="d-flex justify-content-end">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('products.show', $product->id) }}"
                                                        class="btn btn-info mx-1"><i class="fa fa-eye"></i></a>

                                                    <a href="{{ route('products.edit', $product->id) }}"
                                                        class="btn btn-warning mx-1"><i class="fa fa-edit"></i></a>

                                                    <button type="button" class="btn btn-danger mx-1" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="{{ $product->id }}"
                                                        data-name="{{ $product->name }}"
                                                        data-url="{{ route('products.destroy', $product->id) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">{{ __('products.no_data') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title mt-0">{{ __('products.title_delete_product') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <p id="deleteModalMessage"></p>
                    <i class="fa fa-trash text-danger fa-3x mb-3"></i>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4"
                        data-dismiss="modal">{{ __('products.btn_cancel') }}</button>
                    <form id="deleteModalForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn btn-danger px-4">{{ __('products.btn_deleted_product') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const translations = {
                deleteMessage: "{!! __('products.delete_message', ['name' => '__NAME__']) !!}"
            };
            $('#deleteModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const id = button.data('id');
                const name = button.data('name');
                const url = button.data('url');

                const modal = $(this);
                modal.find('#deleteModalMessage').html(
                    translations.deleteMessage.replace('__NAME__', name)
                );
                modal.find('#deleteModalForm').attr('action', url);
            });
        });
    </script>
@endsection
