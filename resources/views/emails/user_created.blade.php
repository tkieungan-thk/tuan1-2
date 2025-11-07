{{-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}


@component('mail::message')
# {{ __('emails.user_created_greeting', ['name' => $user->name]) }}

{{ __('emails.user_created_body') }}

@component('mail::button', ['url' => $resetUrl])
{{ __('emails.user_created_button') }}
@endcomponent

{{ __('emails.user_created_footer') }}
@endcomponent
