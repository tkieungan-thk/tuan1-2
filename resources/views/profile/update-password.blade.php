@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <header class="mb-4">
                <h2 class="h5 text-dark">
                    {{ __('auth.update_password') }}
                </h2>
            </header>

            <form method="post" action="{{ route('password') }}">
                @csrf
                @method('PATCH')

                <div class="form-group mb-3">
                    <label for="update_password_current_password" class="form-label">
                        {{ __('auth.current_password') }}
                    </label>
                    <input id="update_password_current_password" name="current_password" type="password"
                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                        autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="update_password_password" class="form-label">
                        {{ __('auth.new_password') }}
                    </label>
                    <input id="update_password_password" name="password" type="password"
                        class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="update_password_password_confirmation" class="form-label">
                        {{ __('auth.confirm_password') }}
                    </label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                        class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                        autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-primary">{{ __('auth.save_changes') }}</button>

                    @if (session('status') === 'password-updated')
                        <span class="text-success ms-3">{{ __('auth.saved.') }}</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
