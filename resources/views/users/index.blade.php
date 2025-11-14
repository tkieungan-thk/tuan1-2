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
                        <div class="mb-3">
                            <form action="{{ route('users.index') }}" method="GET" class="align-items-center row g-2">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                                        class="form-control" placeholder="{{ __('users.search_placeholder') }}">
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <select name="status" class="form-control">
                                        <option value="">{{ __('users.all_status') }}</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}"
                                                {{ request()->has('status') && request('status') == $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fa fa-search"></i> {{ __('users.filter') }}
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-undo"></i> {{ __('users.reset') }}
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table mb-0 table-hover">
                                <thead class="thead-default">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('users.username') }}</th>
                                        <th>Email</th>
                                        <th th scope="col" colspan="2">{{ __('users.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{!! $user->badge !!}</td>
                                            <td class="d-flex justify-content-end">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="btn btn-warning mx-1"><i class="fa fa-edit"></i></a>
                                                    <button type="button"
                                                        class="btn btn-{{ $user->status->color() }} mx-1"
                                                        data-toggle="modal" data-target="#statusModal"
                                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                        data-status="{{ $user->status->value }}">
                                                        <i class="fa {{ $user->status->icon() }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger mx-1" data-toggle="modal"
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
                    <button type="button" class="btn btn-secondary px-4"
                        data-dismiss="modal">{{ __('users.btn_cancel') }}</button>
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
                    <h5 class="modal-title mt-0">{{ __('users.title_delete_user') }}</h5>
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
                        data-dismiss="modal">{{ __('users.btn_cancel') }}</button>
                    <form id="deleteModalForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4">{{ __('users.btn_deleted_user') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const translations = {
            lockTitle: "{{ __('users.lock_title') }}",
            unlockTitle: "{{ __('users.unlock_title') }}",
            lockMessage: "{{ __('users.lock_message', ['name' => '__NAME__']) }}",
            unlockMessage: "{{ __('users.unlock_message', ['name' => '__NAME__']) }}",
            lockButton: "{{ __('users.lock_button') }}",
            unlockButton: "{{ __('users.unlock_button') }}",
            deleteMessage: "{!! __('users.delete_message', ['name' => '__NAME__']) !!}"
        };

        $('#statusModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const status = button.data('status');
            const UserStatus = @json(array_column($statuses, 'value', 'name'));

            const modal = $(this);
            const isActive = status === UserStatus.ACTIVE;

            modal.find('#statusModalTitle').text(isActive ? translations.lockTitle : translations.unlockTitle);
            modal.find('#statusModalMessage').html(
                (isActive ? translations.lockMessage : translations.unlockMessage).replace('__NAME__', name)
            );

            const header = modal.find('#statusModalHeader');
            const icon = modal.find('#statusModalIcon');
            const buttonSubmit = modal.find('#statusModalButton');

            if (isActive) {
                header.removeClass().addClass('modal-header bg-warning text-dark');
                icon.removeClass().addClass('fa fa-lock text-warning fa-3x mb-3');
                buttonSubmit.removeClass().addClass('btn btn-warning px-4').text(translations.lockButton);
            } else {
                header.removeClass().addClass('modal-header bg-success text-white');
                icon.removeClass().addClass('fa fa-unlock text-success fa-3x mb-3');
                buttonSubmit.removeClass().addClass('btn btn-success px-4').text(translations.unlockButton);
            }

            modal.find('#statusModalForm').attr('action', `/users/${id}/status`);
        });

        $('#deleteModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');

            const modal = $(this);
            modal.find('#deleteModalMessage').html(
                translations.deleteMessage.replace('__NAME__', name)
            );

            modal.find('#deleteModalForm').attr('action', `/users/${id}`);
        });
        $(document).ready(function() {
            $('input[name="keyword"], select[name="status"]').on('input change', function() {
                let keyword = $('input[name="keyword"]').val();
                let status = $('select[name="status"]').val();

                $.ajax({
                    url: "{{ route('users.index') }}",
                    method: 'GET',
                    data: {
                        keyword,
                        status
                    },
                    success: function(response) {
                        $('tbody').html($(response).find('tbody').html());
                    }
                });
            });
        });
    </script>
@endsection
