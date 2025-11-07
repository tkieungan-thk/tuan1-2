@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('users.page_title') }}</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('users.item1') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('users.item2') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
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
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="thead-default">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('users.username') }}</th>
                                        <th>Email</th>
                                        <th>{{ __('users.status') }}</th>
                                        <th>{{ __('users.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->status ? 'Active' : 'Inactive' }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                    <button type="button"
                                                        class="btn {{ $user->status ? 'btn-warning' : 'btn-success' }}"
                                                        data-toggle="modal" data-target="#statusModal"
                                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                        data-status="{{ $user->status }}">
                                                        <i class="fa {{ $user->status ? 'fa-lock' : 'fa-unlock' }}"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="{{ $user->id }}"
                                                        data-name="{{ $user->name }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div id="statusModalHeader" class="modal-header">
                    <h5 class="modal-title mt-0" id="statusModalTitle"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <p id="statusModalMessage"></p>
                    <i id="statusModalIcon" class="fa fa-3x mb-3"></i>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Hủy</button>
                    <form id="statusModalForm" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" id="statusModalButton" class="btn px-4"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title mt-0">Xóa người dùng</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <p id="deleteModalMessage"></p>
                    <i class="fa fa-trash text-danger fa-3x mb-3"></i>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Hủy</button>
                    <form id="deleteModalForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#statusModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const status = button.data('status');

            const modal = $(this);
            const isActive = status == 1;

            modal.find('#statusModalTitle').text(isActive ? 'Khóa tài khoản' : 'Mở khóa tài khoản');
            modal.find('#statusModalMessage').text(
                isActive ?
                `Bạn có chắc chắn muốn KHÓA tài khoản của ${name} không?` :
                `Bạn có chắc chắn muốn MỞ KHÓA tài khoản của ${name} không?`
            );

            const header = modal.find('#statusModalHeader');
            const icon = modal.find('#statusModalIcon');
            const buttonSubmit = modal.find('#statusModalButton');

            if (isActive) {
                header.removeClass().addClass('modal-header bg-warning text-dark');
                icon.removeClass().addClass('fa fa-lock text-warning fa-3x mb-3');
                buttonSubmit.removeClass().addClass('btn btn-warning px-4').text('Khóa');
            } else {
                header.removeClass().addClass('modal-header bg-success text-white');
                icon.removeClass().addClass('fa fa-unlock text-success fa-3x mb-3');
                buttonSubmit.removeClass().addClass('btn btn-success px-4').text('Mở khóa');
            }

            modal.find('#statusModalForm').attr('action', `/users/status/${id}`);
        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            const modal = $(this);
            modal.find('#deleteModalMessage').html(
                `Bạn có chắc chắn muốn <strong>xóa</strong> tài khoản của <strong>${name}</strong> không? Hành động này không thể hoàn tác.`
            );

            modal.find('#deleteModalForm').attr('action', `/users/${id}`);
        });
    </script>
@endsection
