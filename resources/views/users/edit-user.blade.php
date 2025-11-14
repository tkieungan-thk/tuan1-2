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
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('users.item1') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('users.item_update') }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card m-b-30">
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="name">{{ __('users.username') }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="form-control" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('users.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control" required>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('users.password') }}</label>
                        <input type="password" id="password" name="password" class="form-control">
                        @error('password')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('users.confirm_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">{{ __('users.btn_saved') }}</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('users.btn_cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
