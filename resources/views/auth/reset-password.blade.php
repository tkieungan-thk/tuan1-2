@extends('layouts.auth')

@section('title', __('auth.reset_password'))
@section('page-title', __('auth.reset_password'))

@section('content')
    <form class="form-horizontal m-t-30" method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">


        <div class="form-group">
            <div class="col-12">
                <label for="email">{{ __('auth.email') }}</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email"
                    value="{{ old('email', $email) }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password">{{ __('auth.new_password') }}</label>
                <input id="password" type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ __('auth.enter_new_password') }}" required autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password_confirmation">{{ __('auth.confirm_new_password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="{{ __('auth.reenter_new_password') }}" required autocomplete="new-password">
                @error('password_confirmation')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group text-center m-t-20">
            <div class="col-12">
                <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">
                    {{ __('auth.reset_password_button') }}
                </button>
            </div>
        </div>

        <div class="form-group mb-0 row">
            <div class="col-12 m-t-10 text-center">
                <a href="{{ route('login') }}" class="text-muted">{{ __('auth.back_to_login') }}</a>
            </div>
        </div>
    </form>
@endsection
