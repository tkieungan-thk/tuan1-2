@extends('layouts.auth')

@section('title', 'Register')
@section('page-title', 'Register')

@section('content')
    <form class="form-horizontal m-t-30" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <div class="col-12">
                <label for="name">Username</label>
                <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required
                    autofocus placeholder="Enter your username">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="email">Email</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}"
                    required placeholder="Enter your email">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password">Password</label>
                <input id="password" class="form-control" type="password" name="password" required
                    placeholder="Enter your password">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required
                    placeholder="Re-enter your password">
                @error('password_confirmation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="col-12">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                    <label class="custom-control-label font-weight-normal" for="customCheck1">
                        I accept <a href="#" class="text-primary">Terms and Conditions</a>
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group text-center m-t-20">
            <div class="col-12">
                <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">
                    Register
                </button>
            </div>
        </div>

        <div class="form-group mb-0 row">
            <div class="col-12 m-t-10 text-center">
                <a href="{{ route('login') }}" class="text-muted">Already have an account?</a>
            </div>
        </div>
    </form>
@endsection
