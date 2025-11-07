@extends('layouts.auth')

@section('title', __('auth.forgot_password'))
@section('page-title', __('auth.forgot_password'))

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @else
        <div class="alert alert-info mt-3">
            {!! __('auth.enter_email_instruction') !!} </div>
    @endif

    <form class="form-horizontal m-t-30" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <div class="col-12">
                <label for="email">{{ __('auth.email') }}</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('auth.enter_email') }}"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group text-center m-t-20">
            <div class="col-12">
                <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">
                    {{ __('auth.send_reset_link') }}
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
