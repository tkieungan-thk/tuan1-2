@extends('layouts.auth')

@section('title', __('auth.register'))
@section('page-title', __('auth.register'))

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <form class="form-horizontal m-t-30" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <div class="col-12">
                <label for="name">{{ __('auth.username') }}</label>
                <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}"
                    required autofocus placeholder="{{ __('auth.enter_username') }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="email">{{ __('auth.email') }}</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}"
                    required placeholder="{{ __('auth.enter_email') }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password">{{ __('auth.password') }}</label>
                <input id="password" class="form-control" type="password" name="password" required
                    placeholder="{{ __('auth.enter_password') }}">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required
                    placeholder="{{ __('auth.reenter_password') }}">
                @error('password_confirmation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group text-center m-t-20">
            <div class="col-12">
                <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">
                    {{ __('auth.register_button') }}
                </button>
            </div>
        </div>

        <div class="form-group mb-0 row">
            <div class="col-12 m-t-10 text-center">
                <a href="{{ route('login') }}" class="text-muted">{{ __('auth.already_have_account') }}</a>
            </div>
        </div>
    </form>
@endsection
