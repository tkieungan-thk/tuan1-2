@extends('layouts.auth')

@section('title', 'Sign In')
@section('page-title', 'Sign In')

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


    <form class="form-horizontal m-t-30" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <div class="col-12">
                <label for="email">Email</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}"
                    required autofocus placeholder="Enter your email">
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

        <div class="form-group text-center m-t-20">
            <div class="col-12">
                <button class="btn btn-primary btn-block btn-lg waves-effect waves-light" type="submit">Log In</button>
            </div>
        </div>

        <div class="form-group row m-t-30 m-b-0">
            <div class="col-sm-7">
                <a href="" class="text-muted">
                    <i class="fa fa-lock m-r-5"></i> Forgot your password?
                </a>
            </div>
            <div class="col-sm-5 text-right">
                <a href="{{ route('register.form') }}" class="text-muted">Create an account</a>
            </div>
        </div>
    </form>
@endsection

<script>
    setTimeout(function() {
        let successAlert = document.getElementById('alert-success');
        let errorAlert = document.getElementById('alert-error');

        if (successAlert) {
            successAlert.classList.remove('show');
            successAlert.classList.add('fade');
            successAlert.style.display = 'none';
        }

        if (errorAlert) {
            errorAlert.classList.remove('show');
            errorAlert.classList.add('fade');
            errorAlert.style.display = 'none';
        }
    }, 5000);
</script>
