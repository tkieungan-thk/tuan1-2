<section>
    <header class="mb-4">
        <h2 class="h5 text-dark">
            {{ __('Profile Information') }}
        </h2>
        <p class="text-muted">
            {{ __("Your account's basic details.") }}
        </p>
    </header>

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label text-muted">{{ __('Name') }}</label>
                <p class="form-control-plaintext">{{ $user->name }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted">{{ __('Email') }}</label>
                <p class="form-control-plaintext">{{ $user->email }}</p>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2 mb-0 p-2">
                        {{ __('Your email address is unverified.') }}
                    </div>
                @else
                    <div class="text-success small">
                        <i class="mdi mdi-check-circle"></i> {{ __('Email verified') }}
                    </div>
                @endif
            </div>

            @if (isset($user->status))
                <div class="mb-3">
                    <label class="form-label text-muted">{{ __('Status') }}</label>
                    @if ($user->status)
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                    @endif
                </div>
            @endif

        </div>
    </div>
</section>
