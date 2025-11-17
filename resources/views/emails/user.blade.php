@php
    $isCreated = $type === \App\Enums\UserNotificationType::CREATED;
@endphp

<x-mail::message>
    # {{ __('emails.hello') }} {{ $user->name }}

    @if ($isCreated)
        {{ __('emails.account_created') }}

        **{{ __('emails.login_email') }}: {{ $user->email }}
        **{{ __('emails.password') }}: {{ $password }}
    @else
        {{ __('emails.account_updated') }}

        **{{ __('emails.login_email') }}: {{ $user->email }}
        **{{ __('emails.new_password') }}: {{ $password }}
    @endif

    {{ __('emails.thank_you') }}
</x-mail::message>
